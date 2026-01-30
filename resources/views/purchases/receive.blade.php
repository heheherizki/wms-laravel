<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-slate-900">Penerimaan Barang (Receiving)</h1>
            <p class="text-slate-500">Input jumlah barang yang datang fisik hari ini untuk PO: <strong>{{ $purchase->po_number }}</strong></p>
        </div>

        <form action="{{ route('purchases.receive.store', $purchase->id) }}" method="POST">
            @csrf
            
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4">Nama Produk</th>
                            <th class="px-6 py-4 text-center">Dipesan</th>
                            <th class="px-6 py-4 text-center">Sudah Diterima</th>
                            <th class="px-6 py-4 text-center">Sisa (Kurang)</th>
                            <th class="px-6 py-4 w-40 text-center bg-indigo-50">Terima Sekarang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($purchase->details as $item)
                        @php
                            $sisa = $item->quantity - $item->quantity_received;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $item->product->name }}</div>
                                <div class="text-xs text-slate-500">{{ $item->product->sku }}</div>
                            </td>
                            <td class="px-6 py-4 text-center font-bold">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-center text-green-600 font-bold">{{ $item->quantity_received }}</td>
                            <td class="px-6 py-4 text-center text-red-600 font-bold">{{ $sisa }}</td>
                            <td class="px-6 py-4 bg-indigo-50">
                                @if($sisa > 0)
                                    <input type="number" 
                                           name="received_qty[{{ $item->id }}]" 
                                           class="w-full rounded-lg border-slate-300 text-center font-bold text-indigo-700 focus:ring-indigo-500" 
                                           min="0" 
                                           max="{{ $sisa }}" 
                                           value="{{ $sisa }}"> {{-- Default isi Sisa --}}
                                @else
                                    <div class="text-center text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded">LENGKAP</div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('purchases.show', $purchase->id) }}" class="px-6 py-3 border border-slate-300 rounded-lg text-slate-600 font-bold hover:bg-slate-50">Batal</a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 shadow-lg">
                    Simpan Penerimaan Barang
                </button>
            </div>
        </form>
    </div>
</x-app-layout>