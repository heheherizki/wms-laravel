<x-app-layout>
    <div class="max-w-5xl mx-auto">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Laporan Laba Rugi</h1>
                <p class="text-slate-500 text-sm">Profit & Loss Statement (Income Statement)</p>
            </div>
            
            {{-- FILTER FORM --}}
            <form method="GET" action="{{ route('reports.profit_loss') }}" class="flex flex-col md:flex-row gap-2 bg-white p-2 rounded-lg border border-slate-200 shadow-sm">
                <input type="date" name="start_date" value="{{ $startDate }}" class="text-sm rounded-md border-slate-300">
                <span class="self-center text-slate-400">-</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="text-sm rounded-md border-slate-300">
                <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-slate-900">Filter</button>
                <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" target="_blank" class="bg-red-600 text-white px-3 py-2 rounded-md text-sm font-bold hover:bg-red-700 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                </a>
            </form>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
            <div class="p-8">
                
                {{-- HEADER LAPORAN --}}
                <div class="text-center border-b border-slate-100 pb-6 mb-6">
                    <h2 class="text-xl font-bold text-slate-900 uppercase tracking-widest">PT. WMS GUDANG MAKMUR</h2>
                    <p class="text-sm text-slate-500">Periode: {{ date('d F Y', strtotime($startDate)) }} s/d {{ date('d F Y', strtotime($endDate)) }}</p>
                </div>

                {{-- 1. PENDAPATAN --}}
                <div class="mb-8">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-3">Pendapatan (Revenue)</h3>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-slate-700 font-medium">Penjualan Kotor (Sales)</span>
                        <span class="font-mono font-bold text-slate-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- 2. HPP --}}
                <div class="mb-8">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-3">Harga Pokok Penjualan (COGS)</h3>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="text-slate-700 font-medium">Biaya Pembelian Barang (HPP)</span>
                        <span class="font-mono font-bold text-red-600">
                            (Rp {{ number_format($totalCOGS, 0, ',', '.') }})
                        </span>
                    </div>
                    
                    {{-- GROSS PROFIT --}}
                    <div class="flex justify-between items-center py-3 mt-2 bg-slate-50 px-3 rounded-lg border border-slate-200">
                        <span class="text-slate-900 font-bold uppercase text-sm">Laba Kotor (Gross Profit)</span>
                        <span class="font-mono font-bold text-xl text-slate-900">Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- 3. BIAYA OPERASIONAL --}}
                <div class="mb-8">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-3">Biaya Operasional (Expenses)</h3>
                    <div class="space-y-1">
                        @foreach($expenses as $exp)
                            <div class="flex justify-between items-center py-2 border-b border-slate-50 hover:bg-slate-50 px-2 rounded transition-colors">
                                <span class="text-slate-600">{{ $exp->category->name }}</span>
                                <span class="font-mono text-slate-700">Rp {{ number_format($exp->total, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between items-center py-2 mt-2 border-t border-slate-300">
                        <span class="text-slate-700 font-bold italic">Total Biaya Operasional</span>
                        <span class="font-mono font-bold text-red-600">(Rp {{ number_format($totalExpenses, 0, ',', '.') }})</span>
                    </div>
                </div>

                {{-- 4. LABA BERSIH --}}
                <div class="mt-8 pt-6 border-t-2 border-slate-900">
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-2xl font-extrabold text-slate-900 uppercase tracking-tight">Laba Bersih</span>
                            <div class="text-xs text-slate-500 font-medium">Net Profit (Loss)</div>
                        </div>
                        <div class="text-right">
                            <span class="font-mono font-extrabold text-3xl {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                Rp {{ number_format($netProfit, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>
            
            {{-- FOOTER NOTE --}}
            <div class="bg-slate-50 p-4 border-t border-slate-200 text-center text-xs text-slate-400">
                Laporan ini digenerate otomatis oleh sistem WMS pada {{ now()->format('d M Y H:i') }}.
            </div>
        </div>

    </div>
</x-app-layout>