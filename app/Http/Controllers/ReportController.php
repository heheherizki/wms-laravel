<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\User;
use App\Models\SalesOrder;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Supplier; // <--- Jangan lupa import ini
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // 1. DASHBOARD MENU LAPORAN
    public function index()
    {
        return view('reports.index');
    }

    // 2. LAPORAN HISTORI TRANSAKSI (Gudang/Logistik)
    public function history(Request $request)
    {
        $products = Product::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->format('Y-m-d');

        $transactions = $this->getFilteredTransactionQuery($request)
                             ->paginate(20)
                             ->withQueryString();

        return view('reports.history', compact('transactions', 'products', 'users', 'startDate', 'endDate'));
    }

    // 3. EXPORT EXCEL
    public function exportExcel(Request $request)
    {
        $transactions = $this->getFilteredTransactionQuery($request)->get();
        $startDate = $request->start_date ?? Carbon::now()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');

        return Excel::download(new TransactionExport($transactions, $startDate, $endDate), 'laporan-mutasi-stok.xlsx');
    }

    // 4. EXPORT PDF (Mutasi Stok)
    public function exportPdf(Request $request)
    {
        $transactions = $this->getFilteredTransactionQuery($request)->get();
        $startDate = $request->start_date ?? Carbon::now()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');

        $pdf = Pdf::loadView('reports.pdf', compact('transactions', 'startDate', 'endDate'));
        return $pdf->setPaper('a4', 'landscape')->stream('laporan-mutasi-stok.pdf');
    }

    // 5. LAPORAN NILAI ASET
    public function stock()
    {
        $products = Product::orderBy('name')->get();

        $totalItems = $products->sum('stock');
        $totalAssetValue = $products->sum(function($product) {
            return $product->stock * $product->purchase_price; // Gunakan purchase_price (HPP)
        });

        return view('reports.stock', compact('products', 'totalItems', 'totalAssetValue'));
    }

    // 6. LAPORAN PENJUALAN
    public function sales(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->format('Y-m-d');

        $invoices = Invoice::with(['salesOrder.customer'])
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->get();

        $totalRevenue = $invoices->sum('total_amount');
        $totalTransactions = $invoices->count();

        return view('reports.sales', compact('invoices', 'startDate', 'endDate', 'totalRevenue', 'totalTransactions'));
    }

    // 7. LAPORAN PIUTANG (AR AGING)
    public function accountsReceivable()
    {
        $customers = Customer::whereHas('salesOrders.invoices', function($q) {
            $q->whereIn('status', ['unpaid', 'partial']);
        })->with(['salesOrders.invoices' => function($q) {
            $q->whereIn('status', ['unpaid', 'partial']);
        }])->get();

        $report = $customers->map(function($customer) {
            $invoices = $customer->salesOrders->flatMap->invoices;
            
            $data = [
                'customer_id' => $customer->id,
                'name' => $customer->name,
                'total_debt' => 0,
                'not_due' => 0,
                'days_0_30' => 0,
                'days_31_60' => 0,
                'days_61_plus' => 0
            ];

            foreach ($invoices as $inv) {
                // Asumsi di Model Invoice ada Accessor 'remaining_balance'
                // Jika tidak ada, ganti manual: ($inv->total_amount - $inv->amount_paid)
                $balance = $inv->total_amount - $inv->amount_paid;
                
                if ($balance <= 0) continue;

                $data['total_debt'] += $balance;
                $dueDate = Carbon::parse($inv->due_date);
                $now = Carbon::now();

                if ($now->lte($dueDate)) {
                    $data['not_due'] += $balance;
                } else {
                    $diff = $dueDate->diffInDays($now);
                    if ($diff <= 30) $data['days_0_30'] += $balance;
                    elseif ($diff <= 60) $data['days_31_60'] += $balance;
                    else $data['days_61_plus'] += $balance;
                }
            }
            return (object) $data;
        });

        $report = $report->where('total_debt', '>', 0);
        $grandTotal = $report->sum('total_debt');

        return view('reports.receivables', compact('report', 'grandTotal'));
    }

    // 8. LAPORAN HUTANG (AP AGING) - [REVISI]
    public function accountsPayable()
    {
        // Ambil Supplier yang punya PO belum lunas
        $suppliers = Supplier::whereHas('purchases', function($q) {
            $q->whereIn('payment_status', ['unpaid', 'partial'])
              ->where('status', '!=', 'canceled');
        })->with(['purchases' => function($q) {
            $q->whereIn('payment_status', ['unpaid', 'partial'])
              ->where('status', '!=', 'canceled');
        }])->get();

        $report = $suppliers->map(function($supplier) {
            $data = [
                'supplier_id' => $supplier->id,
                'name' => $supplier->name,
                'phone' => $supplier->phone,
                'term_days' => $supplier->term_days ?? 0, // Default 0 jika null
                'total_debt' => 0,
                'not_due' => 0,      
                'days_0_30' => 0,    
                'days_31_60' => 0,   
                'days_61_plus' => 0  
            ];

            foreach ($supplier->purchases as $po) {
                // PERBAIKAN: Gunakan 'total_amount' (bukan grand_total)
                // Hitung sisa hutang per PO
                $balance = $po->total_amount - $po->amount_paid;
                
                // Skip jika sudah lunas (balance <= 0)
                // Gunakan toleransi kecil untuk float
                if ($balance <= 1) continue;

                $data['total_debt'] += $balance;
                
                // Hitung Jatuh Tempo
                $poDate = Carbon::parse($po->date);
                $dueDate = $poDate->copy()->addDays($data['term_days']);
                $now = Carbon::now();

                if ($now->lte($dueDate)) {
                    $data['not_due'] += $balance;
                } else {
                    $diff = $dueDate->diffInDays($now);

                    if ($diff <= 30) {
                        $data['days_0_30'] += $balance;
                    } elseif ($diff <= 60) {
                        $data['days_31_60'] += $balance;
                    } else {
                        $data['days_61_plus'] += $balance;
                    }
                }
            }
            return (object) $data;
        });

        // Filter: Hanya yang punya hutang
        $suppliers = $report->where('total_debt', '>', 0);
        $grandTotalDebt = $suppliers->sum('total_debt');

        return view('reports.debt_index', compact('suppliers', 'grandTotalDebt'));
    }

    // 9. CETAK PDF HUTANG - [REVISI]
    public function accountsPayablePrint()
    {
        $suppliers = Supplier::whereHas('purchases', function($q) {
            $q->whereIn('payment_status', ['unpaid', 'partial'])
              ->where('status', '!=', 'canceled');
        })->with(['purchases' => function($q) {
            $q->whereIn('payment_status', ['unpaid', 'partial'])
              ->where('status', '!=', 'canceled');
        }])->get();

        $report = $suppliers->map(function($supplier) {
            $totalDebt = 0;
            foreach ($supplier->purchases as $po) {
                // PERBAIKAN: Gunakan total_amount
                $balance = $po->total_amount - $po->amount_paid;
                if ($balance > 1) { // Toleransi float
                    $totalDebt += $balance;
                }
            }
            $supplier->total_debt = $totalDebt;
            return $supplier;
        });

        $suppliers = $report->filter(function($s) { return $s->total_debt > 0; });
        $grandTotalDebt = $suppliers->sum('total_debt');

        $pdf = Pdf::loadView('reports.debt_pdf', compact('suppliers', 'grandTotalDebt'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream('Laporan-Hutang-'.date('Y-m-d').'.pdf');
    }

    // 10. FORM & LAPORAN STATEMENT OF ACCOUNT (SOA)
    public function supplierStatement(Request $request)
    {
        $suppliers = Supplier::orderBy('name')->get();
        
        // Default: Kosongkan data jika belum ada supplier yang dipilih
        $statement = collect([]); 
        $openingBalance = 0;
        $endingBalance = 0;
        $selectedSupplier = null;

        if ($request->has('supplier_id') && $request->supplier_id != '') {
            $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->format('Y-m-d');
            $supplierId = $request->supplier_id;
            
            $selectedSupplier = Supplier::findOrFail($supplierId);

            // 1. HITUNG SALDO AWAL (OPENING BALANCE)
            // Semua transaksi SEBELUM startDate
            
            // a. Total Tagihan s/d sebelum start date
            $pastBills = \App\Models\Purchase::where('supplier_id', $supplierId)
                ->where('status', '!=', 'canceled')
                ->where('date', '<', $startDate)
                ->sum('total_amount');

            // b. Total Bayar/Retur s/d sebelum start date
            // Kita join ke purchase untuk filter by supplier
            $pastPayments = \App\Models\PurchasePayment::whereHas('purchase', function($q) use ($supplierId) {
                    $q->where('supplier_id', $supplierId);
                })
                ->where('date', '<', $startDate)
                ->sum('amount');

            $openingBalance = $pastBills - $pastPayments;

            // 2. AMBIL TRANSAKSI PERIODE INI
            
            // a. Tagihan Baru (PO)
            $bills = \App\Models\Purchase::where('supplier_id', $supplierId)
                ->where('status', '!=', 'canceled')
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->map(function($item) {
                    return [
                        'date' => $item->date,
                        'type' => 'TAGIHAN (PO)',
                        'ref' => $item->po_number,
                        'description' => 'Pembelian Barang',
                        'debit' => 0, // Pembayaran
                        'credit' => $item->total_amount, // Hutang Bertambah
                        'original_obj' => $item
                    ];
                });

            // b. Pembayaran & Retur
            $payments = \App\Models\PurchasePayment::whereHas('purchase', function($q) use ($supplierId) {
                    $q->where('supplier_id', $supplierId);
                })
                ->whereBetween('date', [$startDate, $endDate])
                ->get()
                ->map(function($item) {
                    $type = str_contains($item->payment_method, 'Debit') ? 'RETUR / DEBIT NOTE' : 'PEMBAYARAN';
                    return [
                        'date' => $item->date,
                        'type' => $type,
                        'ref' => 'PAY-' . str_pad($item->id, 5, '0', STR_PAD_LEFT),
                        'description' => $item->notes ?? $item->payment_method,
                        'debit' => $item->amount, // Hutang Berkurang
                        'credit' => 0,
                        'original_obj' => $item
                    ];
                });

            // 3. GABUNG DAN SORTIR BERDASARKAN TANGGAL
            $statement = $bills->concat($payments)->sortBy(function($item) {
                return $item['date']; // Sort by date
            });

            // Hitung Ending Balance untuk display summary
            $totalCredit = $statement->sum('credit');
            $totalDebit = $statement->sum('debit');
            $endingBalance = $openingBalance + $totalCredit - $totalDebit;
        }

        // Jika request PDF
        if ($request->has('export') && $request->export == 'pdf') {
            $pdf = Pdf::loadView('reports.statement_pdf', compact('statement', 'openingBalance', 'endingBalance', 'selectedSupplier', 'request'));
            return $pdf->stream('SOA-'.$selectedSupplier->name.'.pdf');
        }

        return view('reports.statement', compact('suppliers', 'statement', 'openingBalance', 'endingBalance', 'selectedSupplier'));
    }

    // --- PRIVATE METHODS ---

    private function getFilteredTransactionQuery(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->format('Y-m-d');

        $query = Transaction::with('product', 'user')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('product_ids')) {
            $query->whereIn('product_id', $request->product_ids);
        }

        return $query->oldest();
    }
}