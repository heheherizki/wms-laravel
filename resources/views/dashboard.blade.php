<x-app-layout>
    <div class="space-y-8">
        
        {{-- Header Section --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dashboard Overview</h1>
                <p class="text-slate-500 mt-1">Halo {{ Auth::user()->name }}, inilah performa bisnis hari ini.</p>
            </div>
            
            <div class="flex flex-wrap gap-3">
                @can('create_purchase')
                <a href="{{ route('purchases.create') }}" class="flex items-center gap-2 bg-white text-slate-700 border border-slate-200 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Buat PO
                </a>
                @endcan
                
                @can('create_sales')
                <a href="{{ route('sales.create') }}" class="flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition-all shadow-sm shadow-indigo-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    Buat Pesanan Baru
                </a>
                @endcan
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            
            {{-- 1. Omzet --}}
            <div class="relative bg-white p-6 rounded-2xl border border-slate-200 shadow-sm overflow-hidden group hover:border-indigo-300 transition-all">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-24 h-24 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Omzet Bulan Ini</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</h3>
                    <p class="text-xs text-slate-400 mt-1">Berdasarkan invoice terbit</p>
                </div>
            </div>

            {{-- 2. Total Aset --}}
            <div class="relative bg-white p-6 rounded-2xl border border-slate-200 shadow-sm overflow-hidden group hover:border-blue-300 transition-all">
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-24 h-24 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-wider">Total Aset Gudang</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</h3>
                    <p class="text-xs text-slate-400 mt-1">{{ number_format($totalProducts) }} SKU Terdaftar</p>
                </div>
            </div>

            {{-- 3. Pending Orders --}}
            <div class="relative bg-white p-6 rounded-2xl border border-slate-200 shadow-sm overflow-hidden group hover:border-orange-300 transition-all cursor-pointer" 
                onclick="window.location='{{ route('sales.index', ['status' => ['pending', 'partial']]) }}'">
                
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-24 h-24 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-orange-600 uppercase tracking-wider">Order Perlu Proses</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $pendingOrders }}</h3>
                    <p class="text-xs text-slate-400 mt-1 group-hover:text-orange-600">Pending & Partial Ship</p>
                </div>
            </div>

            {{-- 4. Stok Menipis (LINK SUDAH DIPERBAIKI) --}}
            <div class="relative bg-white p-6 rounded-2xl border border-slate-200 shadow-sm overflow-hidden group hover:border-red-300 transition-all cursor-pointer" 
                 onclick="window.location='{{ route('products.index', ['status' => 'low']) }}'">
                
                <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-24 h-24 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-red-600 uppercase tracking-wider">Stok Menipis</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-2">{{ $lowStockCount }}</h3>
                    <p class="text-xs text-slate-400 mt-1 group-hover:text-red-600">Perlu Restock Segera &rarr;</p>
                </div>
            </div>

        </div>

        {{-- Charts & Tables --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            
            <div class="xl:col-span-2 space-y-6">
                
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-bold text-slate-800 text-lg">Analisa Stok</h3>
                        <span class="text-xs font-medium bg-slate-100 text-slate-500 px-3 py-1 rounded-full">7 Hari Terakhir</span>
                    </div>
                    <div id="stockChart" class="w-full h-80"></div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="font-bold text-slate-800 text-lg">Produk Terlaris (Bulan Ini)</h3>
                        <span class="text-xs text-slate-400">Top 5 by Volume</span>
                    </div>
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-500 font-semibold">
                            <tr>
                                <th class="px-6 py-3">Produk</th>
                                <th class="px-6 py-3 text-right">Terjual</th>
                                <th class="px-6 py-3 text-right">Sisa Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($topProducts as $item)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ $item->product->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $item->product->sku }}</div>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-indigo-600">
                                    {{ $item->total_qty }}
                                </td>
                                <td class="px-6 py-4 text-right text-slate-600">
                                    {{ $item->product->stock }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-slate-400">Belum ada penjualan bulan ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col h-full">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-slate-800 text-lg">Log Aktivitas</h3>
                    <a href="{{ route('products.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 hover:underline">Semua</a>
                </div>
                
                <div class="flex-grow overflow-y-auto pr-1">
                    <div class="relative border-l border-slate-200 ml-3 space-y-6">
                        @forelse($recentTransactions as $log)
                            <div class="mb-8 ml-6 relative">
                                <span class="absolute -left-[31px] flex items-center justify-center w-8 h-8 rounded-full ring-4 ring-white {{ $log->type == 'in' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                    @if($log->type == 'in')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                    @endif
                                </span>
                                <div class="bg-slate-50 p-3 rounded-lg border border-slate-100">
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-bold text-slate-800 text-sm">{{ $log->product->name }}</h4>
                                        <span class="text-xs font-bold {{ $log->type == 'in' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $log->type == 'in' ? '+' : '-' }}{{ $log->quantity }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-1 truncate">
                                        {{ $log->type == 'in' ? 'Masuk' : 'Keluar' }} • {{ $log->reference }}
                                    </p>
                                    <span class="text-[10px] text-slate-400 block mt-2">
                                        {{ $log->created_at->diffForHumans() }} by {{ $log->user ? $log->user->name : 'System' }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="ml-6">
                                <p class="text-sm text-slate-400 italic">Belum ada aktivitas.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var options = {
                series: [{
                    name: 'Barang Masuk',
                    data: @json($chartData['in'])
                }, {
                    name: 'Barang Keluar',
                    data: @json($chartData['out'])
                }],
                chart: {
                    type: 'area', // Ganti ke area agar lebih cantik
                    height: 320,
                    fontFamily: 'Inter, sans-serif',
                    toolbar: { show: false },
                    zoom: { enabled: false }
                },
                colors: ['#22c55e', '#ef4444'], 
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth', // Garis lengkung halus
                    width: 2
                },
                xaxis: {
                    categories: @json($chartData['labels']),
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: '#64748b', fontSize: '11px' } }
                },
                yaxis: {
                    labels: { style: { colors: '#64748b', fontSize: '11px' } }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 100]
                    }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,
                    padding: { top: 0, right: 0, bottom: 0, left: 10 }
                },
                legend: { position: 'top', horizontalAlign: 'right' },
                tooltip: {
                    theme: 'light',
                    y: { formatter: function (val) { return val + " Unit" } }
                }
            };

            var chart = new ApexCharts(document.querySelector("#stockChart"), options);
            chart.render();
        });
    </script>
</x-app-layout>