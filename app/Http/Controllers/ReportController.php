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

    // 8. LAPORAN HUTANG (AP AGING) - [BARU DITAMBAHKAN]
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
                'term_days' => $supplier->term_days,
                'total_debt' => 0,
                'not_due' => 0,      // Belum jatuh tempo
                'days_0_30' => 0,    // Telat 0-30 hari
                'days_31_60' => 0,   // Telat 31-60 hari
                'days_61_plus' => 0  // Telat > 60 hari
            ];

            foreach ($supplier->purchases as $po) {
                // Hitung sisa hutang per PO
                $balance = $po->total_amount - $po->amount_paid;
                
                if ($balance <= 0) continue;

                $data['total_debt'] += $balance;
                
                // Hitung Jatuh Tempo Berdasarkan Term Supplier
                // Tgl PO + Term Days = Jatuh Tempo
                $poDate = Carbon::parse($po->date);
                $dueDate = $poDate->copy()->addDays($supplier->term_days);
                $now = Carbon::now();

                if ($now->lte($dueDate)) {
                    // Belum Jatuh Tempo
                    $data['not_due'] += $balance;
                } else {
                    // Sudah Lewat Jatuh Tempo
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

        // Kita gunakan view yang sama (debt_index)
        return view('reports.debt_index', compact('suppliers', 'grandTotalDebt'));
    }

    // 9. CETAK PDF HUTANG - [BARU DITAMBAHKAN]
    public function accountsPayablePrint()
    {
        // Logika sama persis dengan accountsPayable, hanya beda return PDF
        // Sebaiknya diloop ulang atau dipisah ke private method jika ingin DRY (Don't Repeat Yourself)
        // Tapi untuk kemudahan copy-paste, saya tulis ulang logic-nya di sini:

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
                $totalDebt += ($po->total_amount - $po->amount_paid);
            }
            // Kita simpan total_debt ke object supplier agar mudah diakses di View PDF
            $supplier->total_debt = $totalDebt;
            return $supplier;
        });

        // Filter supplier yang hutangnya > 0
        $suppliers = $report->filter(function($s) { return $s->total_debt > 0; });
        $grandTotalDebt = $suppliers->sum('total_debt');

        $pdf = Pdf::loadView('reports.debt_pdf', compact('suppliers', 'grandTotalDebt'));
        $pdf->setPaper('a4', 'portrait');
        
        return $pdf->stream('Laporan-Hutang-'.date('Y-m-d').'.pdf');
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