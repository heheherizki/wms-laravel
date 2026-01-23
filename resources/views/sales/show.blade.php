<x-app-layout>
    <div class="max-w-5xl mx-auto">
        
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Detail Sales Order</h1>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-slate-500 font-mono text-sm bg-slate-100 px-2 py-0.5 rounded">{{ $order->so_number }}</span>
                    
                    @if($order->status == 'pending')
                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-bold border border-yellow-200">Pending (Persiapan)</span>
                    @elseif($order->status == 'partial')
                        <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full font-bold border border-orange-200">Partial (Dikirim Sebagian)</span>
                    @elseif($order->status == 'shipped')
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-bold border border-green-200">Shipped (Selesai)</span>
                    @else
                        <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-bold border border-red-200">Batal</span>
                    @endif
                </div>
            </div>
            <a href="{{ route('sales.index') }}" class="text-slate-500 hover:text-slate-800 text-sm font-medium transition-colors">&larr; Kembali ke Daftar</a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 shadow-sm rounded-r relative" role="alert">
                <span class="block sm:inline font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 shadow-sm rounded-r relative" role="alert">
                <span class="block sm:inline font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 p-3 opacity-10">
                    <svg class="w-16 h-16 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Customer</h3>
                <div class="font-bold text-lg text-slate-900">{{ $order->customer->name }}</div>
                <div class="text-sm text-slate-500 mt-1 leading-relaxed">{{ $order->customer->address ?? '-' }}</div>
                <div class="text-sm text-slate-500 mt-1 font-mono">{{ $order->customer->phone ?? '-' }}</div>
            </div>
            
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Detail Order</h3>
                <div class="flex justify-between mb-2 pb-2 border-b border-slate-50">
                    <span class="text-sm text-slate-500">Tanggal Order:</span>
                    <span class="text-sm font-bold text-slate-800">{{ date('d M Y', strtotime($order->date)) }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-slate-500">Salesperson:</span>
                    <span class="text-sm font-bold text-slate-800">{{ $order->user->name }}</span>
                </div>
                @if($order->notes)
                    <div class="mt-3 bg-slate-50 p-2 rounded text-xs text-slate-600 italic border border-slate-100">
                        "{{ $order->notes }}"
                    </div>
                @endif
            </div>

            <div class="bg-slate-900 p-5 rounded-xl shadow-lg text-white flex flex-col justify-center text-center relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Nilai Order</h3>
                <div class="text-3xl font-mono font-bold tracking-tight">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="font-bold text-slate-800">Daftar Barang</h3>
            </div>
            <table class="w-full text-sm text-left">
                <thead class="bg-white text-slate-500 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3">Nama Produk</th>
                        <th class="px-6 py-3 text-center">Jumlah Order</th>
                        <th class="px-6 py-3 text-right">Harga Satuan</th>
                        <th class="px-6 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($order->details as $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">{{ $item->product->name }}</div>
                            <div class="text-xs text-slate-500 font-mono">{{ $item->product->sku }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                {{ $item->quantity }} {{ $item->product->unit }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-mono text-slate-600">
                            Rp {{ number_format($item->price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right font-mono font-bold text-slate-800">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($order->shipments->count() > 0)
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-4">
                <h3 class="text-lg font-bold text-slate-900">Riwayat Pengiriman (Surat Jalan)</h3>
                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-0.5 rounded-full">{{ $order->shipments->count() }}x Kirim</span>
            </div>
            
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3">No. Surat Jalan</th>
                            <th class="px-6 py-3">Tanggal Kirim</th>
                            <th class="px-6 py-3">Catatan / Supir</th>
                            <th class="px-6 py-3 text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($order->shipments as $sj)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-blue-600">
                                <a href="{{ route('shipments.print', $sj->id) }}" target="_blank" class="hover:underline">{{ $sj->shipment_number }}</a>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ date('d/m/Y', strtotime($sj->date)) }}
                            </td>
                            <td class="px-6 py-4 text-slate-500 italic">
                                {{ $sj->notes ?: '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('shipments.print', $sj->id) }}" target="_blank" class="inline-flex items-center gap-1 text-slate-600 hover:text-blue-600 text-xs font-bold border border-slate-300 hover:border-blue-400 px-3 py-1.5 rounded-lg bg-white transition-all shadow-sm">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Cetak
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="sticky bottom-4 z-20 flex flex-col md:flex-row justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-xl gap-4">
            
            <div>
                @if($order->status == 'pending')
                    <form action="{{ route('sales.destroy', $order->id) }}" method="POST" onsubmit="return confirm('APAKAH ANDA YAKIN? Data pesanan ini akan dihapus permanen.');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-sm hover:underline flex items-center gap-1 transition-colors px-2 py-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Batalkan Pesanan
                        </button>
                    </form>
                @endif
            </div>

            <div class="flex flex-wrap gap-3 justify-end items-center">
                
                <a href="{{ route('sales.print_so', $order->id) }}" target="_blank" class="px-4 py-2 border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 hover:border-slate-400 rounded-lg font-bold text-sm flex items-center gap-2 transition-all shadow-sm">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2m-6 9l2 2 4-4"></path></svg>
                    Cetak Picking List (SO)
                </a>

                @if($order->status != 'shipped' && $order->status != 'canceled')
                    <a href="{{ route('shipments.create', $order->id) }}" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold text-sm shadow-md hover:shadow-lg transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Proses Pengiriman Baru
                    </a>
                @endif

            </div>
        </div>

    </div>
</x-app-layout>