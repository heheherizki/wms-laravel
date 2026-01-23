<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Buat Pengiriman Baru</h1>
            <p class="text-slate-500">Untuk Sales Order: <span class="font-bold">{{ $so->so_number }}</span></p>
        </div>

        @if(session('error'))
            <div class="mb-4 bg-red-500 text-white px-4 py-3 rounded-lg shadow-sm font-bold text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('shipments.store', $so->id) }}" method="POST">
            @csrf
            
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mb-6 grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">No. Surat Jalan</label>
                    <input type="text" name="shipment_number" value="{{ $sjNumber }}" readonly class="w-full bg-slate-100 border-slate-300 rounded-lg text-sm font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Kirim</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full border-slate-300 rounded-lg text-sm">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Info Pengiriman (Supir/Ekspedisi/Plat No)</label>
                    <textarea name="notes" rows="2" class="w-full border-slate-300 rounded-lg text-sm" placeholder="Contoh: Supir Budi (B 1234 XX)"></textarea>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Produk</th>
                            <th class="px-6 py-4 text-center">Stok Gudang</th>
                            <th class="px-6 py-4 text-center">Dipesan</th>
                            <th class="px-6 py-4 text-center">Sdh Kirim</th>
                            <th class="px-6 py-4 text-center w-40 bg-blue-50 text-blue-800">Kirim Sekarang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($so->details as $item)
                            @php
                                $remaining = $item->quantity - $item->shipped_quantity;
                                // Jangan tampilkan jika sudah lunas terkirim
                                if($remaining <= 0) continue; 
                            @endphp
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">{{ $item->product->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $item->product->sku }}</div>
                                </td>
                                <td class="px-6 py-4 text-center text-slate-500">
                                    {{ $item->product->stock }} {{ $item->product->unit }}
                                </td>
                                <td class="px-6 py-4 text-center font-bold">
                                    {{ $item->quantity }}
                                </td>
                                <td class="px-6 py-4 text-center text-green-600">
                                    {{ $item->shipped_quantity }}
                                </td>
                                <td class="px-6 py-4 bg-blue-50">
                                    <input type="number" 
                                           name="items[{{ $item->product_id }}]" 
                                           value="{{ $remaining }}" 
                                           min="0" 
                                           max="{{ $remaining }}"
                                           class="w-full border-blue-300 rounded-lg text-center font-bold text-blue-900 focus:ring-blue-500 focus:border-blue-500">
                                    <div class="text-[10px] text-center text-blue-400 mt-1">Max: {{ $remaining }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('sales.show', $so->id) }}" class="px-6 py-3 border border-slate-300 rounded-xl text-slate-600 font-bold hover:bg-slate-50">Batal</a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 shadow-lg">
                    Proses Pengiriman & Potong Stok
                </button>
            </div>
        </form>
    </div>
</x-app-layout>