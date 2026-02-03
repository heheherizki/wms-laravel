<x-app-layout>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Pusat Laporan</h1>
        <p class="text-slate-500">Pilih jenis laporan yang ingin ditampilkan.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- 1. MUTASI STOK --}}
        <a href="{{ route('reports.history') }}" class="block p-6 bg-white border border-slate-200 rounded-xl hover:shadow-lg hover:border-indigo-500 transition-all group">
            <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center text-slate-600 mb-4 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 group-hover:text-indigo-600">Mutasi Stok (History)</h3>
            <p class="text-sm text-slate-500 mt-2">Laporan teknis riwayat keluar-masuk barang per transaksi (Audit Log).</p>
        </a>

        {{-- 2. NILAI ASET --}}
        <a href="{{ route('reports.stock') }}" class="block p-6 bg-white border border-slate-200 rounded-xl hover:shadow-lg hover:border-blue-500 transition-all group">
            <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 mb-4 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-600">Nilai Aset Stok</h3>
            <p class="text-sm text-slate-500 mt-2">Total valuasi barang di gudang berdasarkan Harga Beli (Modal).</p>
        </a>

        {{-- 3. PENJUALAN --}}
        <a href="{{ route('reports.sales') }}" class="block p-6 bg-white border border-slate-200 rounded-xl hover:shadow-lg hover:border-emerald-500 transition-all group">
            <div class="w-12 h-12 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600 mb-4 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600">Laporan Penjualan</h3>
            <p class="text-sm text-slate-500 mt-2">Rekapitulasi omzet penjualan dan performa sales order.</p>
        </a>

        {{-- 4. PIUTANG (AR) --}}
        <a href="{{ route('reports.receivables') }}" class="block p-6 bg-white border border-slate-200 rounded-xl hover:shadow-lg hover:border-red-500 transition-all group">
            <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center text-red-600 mb-4 group-hover:bg-red-600 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 group-hover:text-red-600">Laporan Piutang (AR Aging)</h3>
            <p class="text-sm text-slate-500 mt-2">Monitoring umur hutang customer (Belum Jatuh Tempo s/d Macet).</p>
        </a>

        {{-- 5. HUTANG (AP) - [MENU BARU] --}}
        <a href="{{ route('reports.debt.index') }}" class="block p-6 bg-white border border-slate-200 rounded-xl hover:shadow-lg hover:border-orange-500 transition-all group">
            <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center text-orange-600 mb-4 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 group-hover:text-orange-600">Laporan Hutang (AP Aging)</h3>
            <p class="text-sm text-slate-500 mt-2">Monitoring kewajiban pembayaran ke supplier dan jatuh tempo.</p>
        </a>

        {{-- 6. SUPPLIER STATEMENT (SOA) --}}
        <a href="{{ route('reports.statement') }}" class="block p-6 bg-white border border-slate-200 rounded-xl hover:shadow-lg hover:border-violet-500 transition-all group">
            <div class="w-12 h-12 bg-violet-50 rounded-lg flex items-center justify-center text-violet-600 mb-4 group-hover:bg-violet-600 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 group-hover:text-violet-600">Supplier Statement (SOA)</h3>
            <p class="text-sm text-slate-500 mt-2">Kartu riwayat detail hutang supplier (Rekening Koran).</p>
        </a>

    </div>
</x-app-layout>