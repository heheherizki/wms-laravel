<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\User;
use App\Models\SalesOrder;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Supplier;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\CashTransaction;
use App\Models\ExpenseCategory;
use App\Models\Payment;
use App\Models\PurchasePayment;

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

    // 11. LAPORAN LABA RUGI (PROFIT & LOSS)
    public function profitLoss(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-t');

        // 1. HITUNG OMSET (REVENUE)
        // Ambil semua invoice yang tidak dicancel
        $invoices = Invoice::with(['details.product'])
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', '!=', 'canceled') 
            ->get();

        $totalRevenue = $invoices->sum('total_amount');

        // 2. HITUNG HPP (COGS - Cost of Goods Sold)
        // Rumus: Qty Terjual * Harga Beli Produk (Buy Price)
        $totalCOGS = 0;
        foreach ($invoices as $inv) {
            foreach ($inv->details as $item) {
                // Asumsi: Kita pakai harga beli saat ini dari master product
                // (Untuk sistem advanced, seharusnya pakai FIFO/Average dari Batch stok)
                $buyPrice = $item->product->buy_price ?? 0;
                $totalCOGS += ($item->quantity * $buyPrice);
            }
        }

        // 3. HITUNG LABA KOTOR (GROSS PROFIT)
        $grossProfit = $totalRevenue - $totalCOGS;

        // 4. HITUNG BIAYA OPERASIONAL (EXPENSES)
        // Ambil transaksi tipe 'out' yang punya kategori (bukan transfer)
        $expenses = CashTransaction::whereBetween('date', [$startDate, $endDate])
            ->where('type', 'out')
            ->whereNotNull('expense_category_id') // Hanya biaya, bukan mutasi/transfer
            ->with('category')
            ->selectRaw('expense_category_id, sum(amount) as total')
            ->groupBy('expense_category_id')
            ->get();

        $totalExpenses = $expenses->sum('total');

        // 5. LABA BERSIH (NET PROFIT)
        $netProfit = $grossProfit - $totalExpenses;

        // Jika minta PDF
        if ($request->has('export') && $request->export == 'pdf') {
            $pdf = Pdf::loadView('reports.profit_loss_pdf', compact('startDate', 'endDate', 'totalRevenue', 'totalCOGS', 'grossProfit', 'expenses', 'totalExpenses', 'netProfit'));
            return $pdf->stream('Laba-Rugi-'.$startDate.'-sd-'.$endDate.'.pdf');
        }

        return view('reports.profit_loss', compact('startDate', 'endDate', 'totalRevenue', 'totalCOGS', 'grossProfit', 'expenses', 'totalExpenses', 'netProfit'));
    }

    // 12. LAPORAN ARUS KAS (CASH FLOW)
    public function cashFlow(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-t');

        // 1. HITUNG SALDO AWAL (BEGINNING BALANCE)
        // Rumus: Total semua transaksi SEBELUM start_date
        // Ini agak kompleks, kita simplifikasi: Ambil saldo akun saat ini, lalu kurangi transaksi yang terjadi setelah start_date?
        // Cara paling akurat: Hitung maju dari saldo 0 sejak awal sistem.
        
        // A. Saldo Awal Akun (Total Cash on Hand saat start_date)
        // Kita hitung manual dari history transaksi s/d sebelum start_date
        $initialCash = \App\Models\CashAccount::sum('balance'); 
        // Note: Cara di atas adalah saldo SAAT INI. Untuk dapat saldo MASA LALU, kita harus:
        // Saldo Awal = Saldo Sekarang - (Mutasi Masuk Periode Ini) + (Mutasi Keluar Periode Ini) + (Mutasi Masuk Setelah Periode Ini) ...
        // Untuk simplifikasi di tutorial ini, kita hitung mutasi periode ini saja, lalu Saldo Akhir = Saldo Awal + Mutasi.
        
        // Agar tidak rumit, kita fokus pada ARUS KAS PERIODE INI (Inflow vs Outflow) saja dulu.
        
        // 2. ARUS KAS DARI OPERASIONAL (OPERATING ACTIVITIES)
        
        // A. Penerimaan dari Pelanggan (Sales)
        $cashInSales = Payment::whereBetween('date', [$startDate, $endDate])->sum('amount');

        // B. Pembayaran ke Supplier (Purchases)
        $cashOutPurchase = PurchasePayment::whereBetween('date', [$startDate, $endDate])
            ->where('payment_method', 'not like', '%Debit%') // Abaikan retur potong hutang (non-cash)
            ->sum('amount');

        // C. Biaya Operasional (Expenses)
        $cashOutExpenses = CashTransaction::whereBetween('date', [$startDate, $endDate])
            ->where('type', 'out')
            ->whereNotNull('expense_category_id') // Hanya expense murni
            ->sum('amount');

        // D. Pemasukan Lain-lain (Other Income)
        $cashInOthers = CashTransaction::whereBetween('date', [$startDate, $endDate])
            ->where('type', 'in')
            ->sum('amount');

        // 3. HITUNG TOTAL
        $totalIn = $cashInSales + $cashInOthers;
        $totalOut = $cashOutPurchase + $cashOutExpenses;
        $netCashFlow = $totalIn - $totalOut;

        // Jika minta PDF
        if ($request->has('export') && $request->export == 'pdf') {
            $pdf = Pdf::loadView('reports.cash_flow_pdf', compact('startDate', 'endDate', 'cashInSales', 'cashInOthers', 'cashOutPurchase', 'cashOutExpenses', 'totalIn', 'totalOut', 'netCashFlow'));
            return $pdf->stream('Arus-Kas-'.$startDate.'.pdf');
        }

        return view('reports.cash_flow', compact('startDate', 'endDate', 'cashInSales', 'cashInOthers', 'cashOutPurchase', 'cashOutExpenses', 'totalIn', 'totalOut', 'netCashFlow'));
    }
}