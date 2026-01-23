<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <style>
        .ts-control { border-radius: 0.5rem; padding: 0.5rem; border-color: #cbd5e1; }
        .ts-wrapper.multi .ts-control > div { background: #eff6ff; color: #1e40af; border-radius: 0.25rem; }
    </style>

    <div class="space-y-6">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Riwayat Transaksi</h1>
                <p class="text-slate-500 mt-1">Audit log mutasi barang masuk dan keluar.</p>
            </div>
            <a href="{{ route('reports.index') }}" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition-colors">
                &larr; Kembali ke Menu Laporan
            </a>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <form action="{{ route('reports.history') }}" method="GET" class="space-y-4">
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" 
                            class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" 
                            class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tipe</label>
                        <select name="type" class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                            <option value="">Semua Transaksi</option>
                            <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Barang Masuk (In)</option>
                            <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Barang Keluar (Out)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Oleh User</label>
                        <select name="user_id" class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                            <option value="">Semua User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Filter Produk</label>
                        <select id="select-products" name="product_ids[]" multiple placeholder="Cari dan pilih produk..." autocomplete="off">
                            <option value="">Pilih Produk...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                    {{ in_array($product->id, request('product_ids', [])) ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->sku }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-col gap-2">
                        <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors w-full flex justify-center items-center gap-2 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            Terapkan Filter
                        </button>

                        <div class="flex gap-2">
                            <button type="submit" formaction="{{ route('reports.export_excel') }}" class="w-1/2 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-xs font-medium flex justify-center items-center gap-1 transition-colors shadow-sm">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Excel
                            </button>
                            
                            <button type="submit" formaction="{{ route('reports.export_pdf') }}" class="w-1/2 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-xs font-medium flex justify-center items-center gap-1 transition-colors shadow-sm">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                PDF
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        @if($transactions->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl shadow-sm">
                <p class="text-xs text-blue-600 font-bold uppercase">Total Transaksi</p>
                <p class="text-xl font-bold text-slate-800">{{ $transactions->total() }} <span class="text-sm font-normal text-slate-500">Kali</span></p>
            </div>
            <div class="bg-green-50 border border-green-100 p-4 rounded-xl shadow-sm">
                <p class="text-xs text-green-600 font-bold uppercase">Barang Masuk (Halaman Ini)</p>
                <p class="text-xl font-bold text-slate-800">{{ number_format($transactions->where('type', 'in')->sum('quantity')) }} <span class="text-sm font-normal text-slate-500">Unit</span></p>
            </div>
            <div class="bg-red-50 border border-red-100 p-4 rounded-xl shadow-sm">
                <p class="text-xs text-red-600 font-bold uppercase">Barang Keluar (Halaman Ini)</p>
                <p class="text-xl font-bold text-slate-800">{{ number_format($transactions->where('type', 'out')->sum('quantity')) }} <span class="text-sm font-normal text-slate-500">Unit</span></p>
            </div>
        </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 whitespace-nowrap">Tanggal</th>
                            <th class="px-6 py-4 whitespace-nowrap">Produk</th>
                            <th class="px-6 py-4 whitespace-nowrap">Tipe</th>
                            <th class="px-6 py-4 whitespace-nowrap">Jumlah</th>
                            <th class="px-6 py-4 whitespace-nowrap">Info / Ref</th>
                            <th class="px-6 py-4 whitespace-nowrap">User</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transactions as $log)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y') }}
                                <div class="text-xs text-slate-400">{{ $log->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $log->product->name }}</div>
                                <div class="text-xs text-slate-500">{{ $log->product->sku }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $log->type == 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $log->type == 'in' ? 'Masuk' : 'Keluar' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800">
                                {{ $log->type == 'in' ? '+' : '-' }}{{ number_format($log->quantity) }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                <div class="font-medium">{{ $log->reference }}</div>
                                <div class="text-slate-400 italic truncate max-w-xs">{{ $log->notes }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 whitespace-nowrap">
                                {{ $log->user ? $log->user->name : 'System' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                Tidak ada data transaksi yang sesuai filter ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $transactions->links() }}
            </div>
        </div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function(){
            new TomSelect("#select-products",{
                plugins: ['remove_button'],
                create: false,
                maxItems: null,
                placeholder: 'Cari dan pilih produk...',
                render: {
                    option: function(data, escape) {
                        return '<div>' +
                                '<span class="font-bold">' + escape(data.text.split('(')[0]) + '</span>' +
                                '<span class="text-xs text-slate-400 ml-2">' + escape(data.text.split('(')[1] || '') + '</span>' +
                            '</div>';
                    }
                }
            });
        });
    </script>
</x-app-layout>