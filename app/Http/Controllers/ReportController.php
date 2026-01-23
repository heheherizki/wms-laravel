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
}