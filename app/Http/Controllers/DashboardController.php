<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\SalesOrder;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // =====================================================================
        // 1. STATISTIK UTAMA (KARTU ATAS)
        // =====================================================================
        
        // Total Produk (SKU Aktif)
        $totalProducts = Product::count();
        
        // Stok Menipis (Berdasarkan kolom min_stock di database)
        $lowStockCount = Product::whereColumn('stock', '<=', 'min_stock')->count();
        
        // Total Nilai Aset Gudang (Stok saat ini * Harga Beli)
        $totalAssetValue = Product::sum(DB::raw('stock * buy_price'));

        // Omzet Bulan Ini (Berdasarkan Invoice yang terbit bulan ini)
        $monthlyRevenue = Invoice::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        // Order Perlu Proses (Status Pending atau Partial)
        $pendingOrders = SalesOrder::whereIn('status', ['pending', 'partial'])->count();

        // Mutasi Harian (Untuk Card tambahan jika diperlukan)
        $todayIn = Transaction::where('type', 'in')
            ->whereDate('created_at', Carbon::today())
            ->sum('quantity');

        $todayOut = Transaction::where('type', 'out')
            ->whereDate('created_at', Carbon::today())
            ->sum('quantity');


        // =====================================================================
        // 2. PRODUK TERLARIS (TOP 5 BULAN INI)
        // =====================================================================
        $topProducts = Transaction::select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->where('type', 'out') // Hanya transaksi keluar
            ->whereMonth('created_at', Carbon::now()->month)
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(5)
            ->with('product')
            ->get();


        // =====================================================================
        // 3. DATA GRAFIK (7 HARI TERAKHIR)
        // =====================================================================
        $chartData = $this->getChartData();


        // =====================================================================
        // 4. AKTIVITAS TERKINI (RECENT ACTIVITY)
        // =====================================================================
        $recentTransactions = Transaction::with(['product', 'user'])
            ->latest()
            ->take(6)
            ->get();


        return view('dashboard', compact(
            'totalProducts', 
            'lowStockCount', 
            'totalAssetValue',
            'monthlyRevenue',
            'pendingOrders',
            'todayIn',
            'todayOut',
            'topProducts',
            'chartData', 
            'recentTransactions'
        ));
    }

    /**
     * Helper Private: Menyiapkan data untuk ApexCharts
     * Loop 7 hari ke belakang agar grafik tetap rapi meskipun tidak ada transaksi
     */
    private function getChartData()
    {
        $labels = [];
        $inData = [];
        $outData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $formattedDate = $date->format('Y-m-d');
            
            // Label Sumbu X (Contoh: 20 Jan)
            $labels[] = $date->format('d M');

            // Hitung Total Masuk
            $inData[] = Transaction::where('type', 'in')
                ->whereDate('created_at', $formattedDate)
                ->sum('quantity');

            // Hitung Total Keluar
            $outData[] = Transaction::where('type', 'out')
                ->whereDate('created_at', $formattedDate)
                ->sum('quantity');
        }

        return [
            'labels' => $labels,
            'in'     => $inData,
            'out'    => $outData
        ];
    }
}