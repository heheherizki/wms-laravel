<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\User;
use App\Models\SalesOrder;
use App\Models\Invoice;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;

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

        // PENTING: Untuk Web View, kita pakai PAGINATE agar bisa ada halaman 1, 2, dst
        // Kita panggil query builder-nya, lalu chain dengan paginate
        $transactions = $this->getFilteredTransactionQuery($request)
                             ->paginate(20)
                             ->withQueryString(); // Agar filter tetap ada saat ganti halaman

        return view('reports.history', compact('transactions', 'products', 'users', 'startDate', 'endDate'));
    }

    // 3. EXPORT EXCEL
    public function exportExcel(Request $request)
    {
        // PENTING: Untuk Export, kita pakai GET (Ambil Semua Data)
        $transactions = $this->getFilteredTransactionQuery($request)->get();
        
        $startDate = $request->start_date ?? Carbon::now()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');

        return Excel::download(new TransactionExport($transactions, $startDate, $endDate), 'laporan-mutasi-stok.xlsx');
    }

    // 4. EXPORT PDF
    public function exportPdf(Request $request)
    {
        // PENTING: Untuk Export, kita pakai GET (Ambil Semua Data)
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
            return $product->stock * $product->buy_price;
        });

        return view('reports.stock', compact('products', 'totalItems', 'totalAssetValue'));
    }

    // 6. LAPORAN PENJUALAN (REALISASI BERDASARKAN INVOICE)
    public function sales(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->format('Y-m-d');

        // Ambil data INVOICE (Faktur) bukan Sales Order
        $invoices = Invoice::with(['salesOrder.customer'])
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled') // Asumsi jika ada fitur cancel invoice
            ->latest()
            ->get();

        // Hitung total dari Invoice yang terbit
        $totalRevenue = $invoices->sum('total_amount');
        $totalTransactions = $invoices->count();

        return view('reports.sales', compact('invoices', 'startDate', 'endDate', 'totalRevenue', 'totalTransactions'));
    }
    
    // --- PRIVATE METHODS ---

    /**
     * Mengembalikan Query Builder (Belum dieksekusi dengan get/paginate)
     * Ini kuncinya agar fleksibel bisa dipaginate atau diget semua.
     */
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

        // Return Query Builder (Tanpa ->get())
        return $query->oldest();
    }

    // LAPORAN PIUTANG (AR AGING)
    public function accountsReceivable()
    {
        // Ambil customer yang punya invoice BELUM LUNAS (Partial / Unpaid)
        $customers = Customer::whereHas('salesOrders.invoices', function($q) {
            $q->whereIn('status', ['unpaid', 'partial']);
        })->with(['salesOrders.invoices' => function($q) {
            $q->whereIn('status', ['unpaid', 'partial']);
        }])->get();

        $report = $customers->map(function($customer) {
            $invoices = $customer->salesOrders->flatMap->invoices;
            
            // Inisialisasi Bucket
            $data = [
                'customer_id' => $customer->id,
                'name' => $customer->name,
                'total_debt' => 0,
                'not_due' => 0,    // Belum jatuh tempo
                'days_0_30' => 0,  // Lewat 0-30 hari
                'days_31_60' => 0, // Lewat 31-60 hari
                'days_61_plus' => 0 // Lewat > 60 hari
            ];

            foreach ($invoices as $inv) {
                // Hitung sisa tagihan per invoice (Total - Paid)
                // Kita gunakan helper attribute yang sudah dibuat di Model Invoice
                $balance = $inv->remaining_balance; 
                
                if ($balance <= 0) continue; // Skip jika lunas (safety check)

                $data['total_debt'] += $balance;
                $dueDate = Carbon::parse($inv->due_date);
                $now = Carbon::now();

                if ($now->lte($dueDate)) {
                    // Belum Jatuh Tempo
                    $data['not_due'] += $balance;
                } else {
                    // Sudah Jatuh Tempo, hitung selisih hari
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

        // Filter: Hanya tampilkan yang punya hutang
        $report = $report->where('total_debt', '>', 0);
        
        $grandTotal = $report->sum('total_debt');

        return view('reports.receivables', compact('report', 'grandTotal'));
    }
}