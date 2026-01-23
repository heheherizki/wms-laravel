<x-app-layout>
    <div class="max-w-5xl mx-auto">
        
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Detail Purchase Order</h1>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-slate-500 text-sm">{{ $purchase->po_number }}</span>
                    @if($purchase->status == 'pending')
                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-bold">Pending (Menunggu Barang)</span>
                    @elseif($purchase->status == 'completed')
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-bold">Selesai (Stok Masuk)</span>
                    @else
                        <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-bold">Dibatalkan</span>
                    @endif
                </div>
            </div>
            <a href="{{ route('purchases.index') }}" class="text-slate-500 hover:text-slate-800 text-sm font-medium">&larr; Kembali ke List</a>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-sm font-bold text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-500 text-white px-4 py-3 rounded-lg shadow-sm font-bold text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase mb-2">Supplier</h3>
                <div class="font-bold text-lg text-slate-800">{{ $purchase->supplier->name }}</div>
                <div class="text-sm text-slate-500 mt-1">{{ $purchase->supplier->address }}</div>
                <div class="text-sm text-slate-500">{{ $purchase->supplier->phone }}</div>
            </div>
            
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase mb-2">Informasi PO</h3>
                <div class="flex justify-between mb-1">
                    <span class="text-sm text-slate-600">Tanggal Buat:</span>
                    <span class="text-sm font-bold text-slate-800">{{ date('d M Y', strtotime($purchase->date)) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-slate-600">Dibuat Oleh:</span>
                    <span class="text-sm font-bold text-slate-800">{{ $purchase->user->name }}</span>
                </div>
                @if($purchase->notes)
                    <div class="mt-3 pt-3 border-t border-slate-100">
                        <span class="text-xs text-slate-400 block">Catatan:</span>
                        <p class="text-sm text-slate-600 italic">"{{ $purchase->notes }}"</p>
                    </div>
                @endif
            </div>

            <div class="bg-slate-800 p-5 rounded-xl shadow-sm text-white flex flex-col justify-center text-center">
                <h3 class="text-xs font-bold text-slate-400 uppercase mb-1">Grand Total</h3>
                <div class="text-3xl font-mono font-bold">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Produk</th>
                        <th class="px-6 py-4 text-center">Qty</th>
                        <th class="px-6 py-4 text-right">Harga Satuan</th>
                        <th class="px-6 py-4 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($purchase->details as $item)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">{{ $item->product->name }}</div>
                            <div class="text-xs text-slate-500">{{ $item->product->sku }}</div>
                        </td>
                        <td class="px-6 py-4 text-center font-bold">
                            {{ $item->quantity }} {{ $item->product->unit }}
                        </td>
                        <td class="px-6 py-4 text-right font-mono text-slate-600">
                            Rp {{ number_format($item->buy_price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right font-mono font-bold text-slate-800">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-between items-center bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <div>
                @if($purchase->status == 'pending')
                    <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus PO ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-sm underline">
                            Hapus PO (Batalkan)
                        </button>
                    </form>
                @endif
            </div>

            <div class="flex gap-3">
                <a href="{{ route('purchases.print', $purchase->id) }}" target="_blank" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 font-bold text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak PDF
                </a>

                @if($purchase->status == 'pending')
                <a href="{{ route('purchases.edit', $purchase->id) }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 font-bold text-sm">
                    ✏️ Edit PO
                </a>
                @endif

                @if($purchase->status == 'pending')
                    <form action="{{ route('purchases.complete', $purchase->id) }}" method="POST" onsubmit="return confirm('Apakah barang sudah datang fisik? Stok akan ditambahkan otomatis.');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold text-sm shadow-lg hover:shadow-xl transition-all">
                            ✅ Terima Barang (Masuk Stok)
                        </button>
                    </form>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>