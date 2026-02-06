<x-app-layout>
    <div class="max-w-5xl mx-auto">
        
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Proses Pengiriman Baru</h1>
                <p class="text-sm text-slate-500">Buat Surat Jalan untuk SO: <span class="font-mono font-bold text-indigo-600">{{ $so->so_number }}</span></p>
            </div>
            <a href="{{ route('sales.show', $so->id) }}" class="text-slate-500 hover:text-slate-800 text-sm font-medium transition-colors">
                &larr; Batal & Kembali
            </a>
        </div>

        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg shadow-sm">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <h3 class="font-bold text-blue-800 text-sm">Informasi Gudang</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        Sistem otomatis membatasi jumlah input berdasarkan <strong>Sisa Pesanan</strong> dan <strong>Stok Fisik Tersedia</strong>.
                        Pastikan barang sudah disiapkan (picking) sebelum membuat Surat Jalan.
                    </p>
                </div>
            </div>
        </div>

        <form action="{{ route('shipments.store', $so->id) }}" method="POST">
            @csrf
            
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden mb-6">
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6 bg-slate-50 border-b border-slate-200">
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">No. Surat Jalan (Auto)</label>
                        <input type="text" name="shipment_number" value="{{ $sjNumber }}" readonly 
                               class="w-full bg-slate-200 border-slate-300 rounded-lg text-sm font-mono font-bold text-slate-600 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Pengiriman</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                               class="w-full border-slate-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan / Supir / Plat No</label>
                        <input type="text" name="notes" placeholder="Contoh: Supir Budi (B 1234 XX)" 
                               class="w-full border-slate-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Daftar Barang yang Akan Dikirim
                    </h3>

                    <div class="overflow-x-auto border border-slate-200 rounded-lg">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-100 text-slate-600 font-bold border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3 w-1/3">Produk</th>
                                    <th class="px-4 py-3 text-center bg-blue-50 text-blue-800">Sisa Pesanan</th>
                                    <th class="px-4 py-3 text-center bg-yellow-50 text-yellow-800">Stok Gudang</th>
                                    <th class="px-4 py-3 text-center w-40">Qty Kirim Sekarang</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($so->details as $item)
                                    @php
                                        // Logic Matematika
                                        $ordered   = $item->quantity;
                                        $shipped   = $item->shipped_quantity;
                                        $remaining = $ordered - $shipped;
                                        $stock     = $item->product->stock;
                                        
                                        // Tentukan Max Input (Tidak boleh lebih dari sisa pesanan DAN tidak boleh lebih dari stok)
                                        $maxInput  = min($remaining, $stock);
                                        
                                        // Status Row
                                        $isCompleted = $remaining <= 0;
                                        $isOutOfStock = !$isCompleted && $stock <= 0;
                                    @endphp

                                    <tr class="hover:bg-slate-50 transition-colors {{ $isCompleted ? 'bg-slate-50 opacity-60' : '' }}">
                                        
                                        <td class="px-4 py-3">
                                            <div class="font-bold text-slate-800">{{ $item->product->name }}</div>
                                            <div class="text-xs text-slate-500 font-mono">{{ $item->product->sku }}</div>
                                            @if($isCompleted)
                                                <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold mt-1 inline-block">Selesai Dikirim</span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3 text-center bg-blue-50/30">
                                            <div class="text-lg font-bold text-blue-700">{{ $remaining }}</div>
                                            <div class="text-[10px] text-slate-400">dari total {{ $ordered }}</div>
                                        </td>

                                        <td class="px-4 py-3 text-center bg-yellow-50/30">
                                            @if($stock >= $remaining && !$isCompleted)
                                                <div class="text-lg font-bold text-green-600">{{ $stock }}</div>
                                                <div class="text-[10px] text-green-600">Stok Cukup</div>
                                            @elseif($stock < $remaining && $stock > 0 && !$isCompleted)
                                                <div class="text-lg font-bold text-orange-600">{{ $stock }}</div>
                                                <div class="text-[10px] text-orange-600">Stok Parsial</div>
                                            @elseif($stock <= 0 && !$isCompleted)
                                                <div class="text-lg font-bold text-red-600">0</div>
                                                <div class="text-[10px] text-red-600 font-bold">Stok Habis!</div>
                                            @else
                                                <div class="text-slate-400 font-bold">-</div>
                                            @endif
                                        </td>

                                        <td class="px-4 py-3">
                                            @if(!$isCompleted && $stock > 0)
                                                <div class="relative">
                                                    <input type="number" 
                                                           name="items[{{ $item->product_id }}]" 
                                                           value="{{ $maxInput }}" 
                                                           min="0" 
                                                           max="{{ $maxInput }}"
                                                           class="w-full text-center font-bold border-indigo-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm"
                                                    >
                                                    <span class="absolute right-8 top-2.5 text-xs text-slate-400 pointer-events-none">{{ $item->product->unit }}</span>
                                                </div>
                                                @if($maxInput < $remaining)
                                                    <p class="text-[10px] text-red-500 mt-1 text-center leading-tight">
                                                        Maks {{ $maxInput }} (Terbatas Stok)
                                                    </p>
                                                @endif
                                            @elseif($isOutOfStock)
                                                <input type="number" value="0" disabled class="w-full text-center bg-slate-100 border-slate-200 text-slate-400 rounded-lg cursor-not-allowed">
                                            @else
                                                <div class="text-center text-green-600 font-bold text-xs">
                                                    ✓ Terpenuhi
                                                </div>
                                                <input type="hidden" name="items[{{ $item->product_id }}]" value="0">
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-col md:flex-row justify-between items-center gap-4">
                    
                    {{-- FITUR BARU: CHECKBOX AUTO INVOICE --}}
                    <div class="flex items-center gap-2 bg-white border border-indigo-200 px-4 py-2 rounded-lg shadow-sm">
                        <input type="checkbox" name="create_invoice" id="create_invoice" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <label for="create_invoice" class="text-sm font-bold text-slate-700 cursor-pointer select-none">
                            Otomatis Buat Invoice
                        </label>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="text-xs text-slate-500 text-right hidden md:block">
                            * Stok fisik otomatis berkurang.<br>
                            * Status Order menjadi Partial/Shipped.
                        </div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg shadow-md transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Proses Pengiriman
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>