<x-app-layout>
    <div class="max-w-4xl mx-auto">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Laporan Arus Kas</h1>
                <p class="text-slate-500 text-sm">Cash Flow Statement (Direct Method)</p>
            </div>
            
            <form method="GET" action="{{ route('reports.cash_flow') }}" class="flex gap-2 bg-white p-2 rounded-lg border border-slate-200 shadow-sm">
                <input type="date" name="start_date" value="{{ $startDate }}" class="text-sm rounded-md border-slate-300">
                <span class="self-center text-slate-400">-</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="text-sm rounded-md border-slate-300">
                <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-slate-900">Filter</button>
                <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" target="_blank" class="bg-red-600 text-white px-3 py-2 rounded-md text-sm font-bold hover:bg-red-700 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                </a>
            </form>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden p-8">
            
            <div class="text-center border-b border-slate-100 pb-6 mb-6">
                <h2 class="text-xl font-bold text-slate-900 uppercase tracking-widest">PT. WMS GUDANG MAKMUR</h2>
                <p class="text-sm text-slate-500">Periode: {{ date('d F Y', strtotime($startDate)) }} s/d {{ date('d F Y', strtotime($endDate)) }}</p>
            </div>

            {{-- ARUS KAS MASUK --}}
            <div class="mb-8">
                <h3 class="text-sm font-bold text-green-600 uppercase tracking-wider mb-2 border-b-2 border-green-100 pb-1">1. Arus Kas Masuk (Cash In)</h3>
                <div class="space-y-3 pl-4">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Penerimaan dari Pelanggan (Sales)</span>
                        <span class="font-mono text-slate-800 font-bold">Rp {{ number_format($cashInSales, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Pemasukan Lain-lain</span>
                        <span class="font-mono text-slate-800 font-bold">Rp {{ number_format($cashInOthers, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-slate-100">
                        <span class="font-bold text-slate-800">Total Kas Masuk</span>
                        <span class="font-mono text-green-600 font-bold text-lg">Rp {{ number_format($totalIn, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- ARUS KAS KELUAR --}}
            <div class="mb-8">
                <h3 class="text-sm font-bold text-red-600 uppercase tracking-wider mb-2 border-b-2 border-red-100 pb-1">2. Arus Kas Keluar (Cash Out)</h3>
                <div class="space-y-3 pl-4">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Pembayaran ke Supplier (Hutang Dagang)</span>
                        <span class="font-mono text-slate-800">Rp {{ number_format($cashOutPurchase, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Biaya Operasional & Pengeluaran Lain</span>
                        <span class="font-mono text-slate-800">Rp {{ number_format($cashOutExpenses, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-slate-100">
                        <span class="font-bold text-slate-800">Total Kas Keluar</span>
                        <span class="font-mono text-red-600 font-bold text-lg">(Rp {{ number_format($totalOut, 0, ',', '.') }})</span>
                    </div>
                </div>
            </div>

            {{-- NET CASH FLOW --}}
            <div class="bg-slate-900 text-white p-6 rounded-xl flex justify-between items-center shadow-lg">
                <div>
                    <h3 class="text-lg font-bold uppercase tracking-widest">Surplus / (Defisit) Kas</h3>
                    <p class="text-slate-400 text-sm">Kenaikan/Penurunan Bersih Kas Periode Ini</p>
                </div>
                <div class="text-3xl font-mono font-bold {{ $netCashFlow >= 0 ? 'text-green-400' : 'text-red-400' }}">
                    {{ $netCashFlow >= 0 ? '+' : '' }} Rp {{ number_format($netCashFlow, 0, ',', '.') }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>