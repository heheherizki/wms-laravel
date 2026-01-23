<x-app-layout>
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Laporan Nilai Aset Stok</h1>
                <p class="text-slate-500 text-sm">Posisi per tanggal: {{ date('d F Y') }}</p>
            </div>
            <a href="{{ route('reports.index') }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm">&larr; Kembali ke Menu</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-blue-600 text-white p-6 rounded-xl shadow-lg">
                <div class="text-blue-100 text-xs font-bold uppercase mb-1">Total Nilai Aset (Harga Beli)</div>
                <div class="text-3xl font-mono font-bold">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white border border-slate-200 p-6 rounded-xl shadow-sm">
                <div class="text-slate-500 text-xs font-bold uppercase mb-1">Total Fisik Barang</div>
                <div class="text-3xl font-mono font-bold text-slate-800">{{ number_format($totalItems) }} <span class="text-sm font-normal text-slate-400">Unit</span></div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Nama Produk</th>
                        <th class="px-6 py-4 text-center">Stok Fisik</th>
                        <th class="px-6 py-4 text-right">Harga Beli (Satuan)</th>
                        <th class="px-6 py-4 text-right">Subtotal Aset</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($products as $product)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-3">
                            <div class="font-bold text-slate-800">{{ $product->name }}</div>
                            <div class="text-xs text-slate-500">{{ $product->sku }}</div>
                        </td>
                        <td class="px-6 py-3 text-center font-bold">
                            {{ $product->stock }} {{ $product->unit }}
                        </td>
                        <td class="px-6 py-3 text-right text-slate-600">
                            Rp {{ number_format($product->buy_price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3 text-right font-bold text-slate-800">
                            Rp {{ number_format($product->stock * $product->buy_price, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 border-t border-slate-200 font-bold">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right uppercase text-slate-600">Total Aset</td>
                        <td class="px-6 py-4 text-right text-blue-700 text-lg">
                            Rp {{ number_format($totalAssetValue, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-app-layout>