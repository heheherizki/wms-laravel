<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Detail Pengiriman</h1>
                <p class="text-slate-500 text-sm">No. SJ: {{ $shipment->shipment_number }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('shipments.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 text-sm font-bold">
                    &larr; Kembali
                </a>
                <a href="{{ route('shipments.print', $shipment->id) }}" target="_blank" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Surat Jalan
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
            <div class="p-6 border-b border-slate-200 bg-slate-50 grid grid-cols-2 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase">Tanggal Kirim</label>
                    <div class="text-slate-900 font-medium">{{ date('d M Y', strtotime($shipment->date)) }}</div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase">Referensi Order</label>
                    <a href="{{ route('sales.show', $shipment->sales_order_id) }}" class="text-indigo-600 font-bold hover:underline">
                        {{ $shipment->salesOrder->so_number }}
                    </a>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase">Customer</label>
                    <div class="text-slate-900 font-medium">{{ $shipment->salesOrder->customer->name }}</div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase">Operator</label>
                    <div class="text-slate-900 font-medium">{{ $shipment->user->name ?? '-' }}</div>
                </div>
            </div>
            
            <div class="p-6">
                <h3 class="font-bold text-slate-800 mb-4">Barang Dikirim</h3>
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-100 text-slate-600 font-bold">
                        <tr>
                            <th class="px-4 py-2 rounded-l-lg">Nama Produk</th>
                            <th class="px-4 py-2">Kode</th>
                            <th class="px-4 py-2 text-center rounded-r-lg">Qty Dikirim</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($shipment->details as $detail)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $detail->product->name }}</td>
                            <td class="px-4 py-3 text-slate-500 font-mono text-xs">{{ $detail->product->sku }}</td>
                            <td class="px-4 py-3 text-center font-bold text-indigo-600">{{ $detail->quantity }} {{ $detail->product->unit }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                @if($shipment->notes)
                    <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-100">
                        <span class="text-xs font-bold text-yellow-800 uppercase block mb-1">Catatan Pengiriman:</span>
                        <p class="text-sm text-yellow-900 italic">{{ $shipment->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>