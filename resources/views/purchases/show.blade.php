<x-app-layout>
    <div class="max-w-5xl mx-auto">
        
        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Detail Purchase Order</h1>
                <div class="flex items-center gap-3 mt-1">
                    <span class="font-mono text-slate-500 font-bold text-lg">{{ $purchase->po_number }}</span>
                    
                    {{-- BADGE STATUS LOGISTIK --}}
                    @if($purchase->status == 'pending')
                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2.5 py-0.5 rounded-full font-bold border border-yellow-200">
                            Pending / Proses
                        </span>
                    @elseif($purchase->status == 'completed')
                        <span class="bg-blue-100 text-blue-800 text-xs px-2.5 py-0.5 rounded-full font-bold border border-blue-200 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Selesai (Full Received)
                        </span>
                    @elseif($purchase->status == 'canceled')
                        <span class="bg-slate-100 text-slate-600 text-xs px-2.5 py-0.5 rounded-full font-bold border border-slate-300">
                            Dibatalkan
                        </span>
                    @endif
                </div>
            </div>
            <a href="{{ route('purchases.index') }}" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 font-bold text-sm transition-colors">
                &larr; Kembali ke List
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded shadow-sm font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded shadow-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        {{-- INFO CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- 1. SUPPLIER INFO --}}
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 p-2 opacity-10">
                    <svg class="w-16 h-16 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <h3 class="text-xs font-bold text-slate-400 uppercase mb-3">Informasi Supplier</h3>
                <div class="font-bold text-lg text-slate-800">{{ $purchase->supplier->name }}</div>
                <div class="text-sm text-slate-500 mt-1">{{ $purchase->supplier->address ?? 'Alamat tidak tersedia' }}</div>
                <div class="text-sm text-slate-500">{{ $purchase->supplier->phone ?? '-' }}</div>
            </div>
            
            {{-- 2. PO DETAILS --}}
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-xs font-bold text-slate-400 uppercase mb-3">Detail Pesanan</h3>
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-slate-600">Tanggal PO:</span>
                    <span class="text-sm font-bold text-slate-800">{{ date('d M Y', strtotime($purchase->date)) }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-slate-600">Dibuat Oleh:</span>
                    <span class="text-sm font-bold text-slate-800">{{ $purchase->user->name }}</span>
                </div>
                @if($purchase->notes)
                    <div class="mt-3 pt-3 border-t border-slate-100">
                        <span class="text-xs text-slate-400 block mb-1">Catatan:</span>
                        <p class="text-sm text-slate-600 italic bg-slate-50 p-2 rounded border border-slate-100">
                            "{{ $purchase->notes }}"
                        </p>
                    </div>
                @endif
            </div>

            {{-- 3. FINANCIAL SUMMARY --}}
            <div class="bg-slate-800 p-5 rounded-xl shadow-lg text-white flex flex-col justify-center text-center relative overflow-hidden">
                {{-- Background Decoration --}}
                <div class="absolute -top-6 -right-6 w-24 h-24 bg-slate-700 rounded-full opacity-50 blur-xl"></div>
                
                <h3 class="text-xs font-bold text-slate-400 uppercase mb-1 relative z-10">Total Tagihan</h3>
                <div class="text-3xl font-mono font-bold relative z-10">
                    Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                </div>
                
                <div class="mt-4 pt-3 border-t border-slate-700 flex justify-between items-center text-xs relative z-10">
                    <span>Status Bayar:</span>
                    @if($purchase->payment_status == 'paid')
                        <span class="text-green-400 font-bold bg-green-900/30 px-2 py-1 rounded">LUNAS</span>
                    @elseif($purchase->payment_status == 'partial')
                        <span class="text-orange-400 font-bold bg-orange-900/30 px-2 py-1 rounded">CICILAN</span>
                    @else
                        <span class="text-red-400 font-bold bg-red-900/30 px-2 py-1 rounded">BELUM LUNAS</span>
                    @endif
                </div>
                <div class="mt-1 text-xs text-slate-400 text-right relative z-10">
                    Sisa: Rp {{ number_format($purchase->total_amount - $purchase->amount_paid, 0, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- TABEL BARANG (ITEMS) --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-8">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                <h3 class="font-bold text-slate-700">Daftar Barang & Progress Penerimaan</h3>
            </div>
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 w-1/3">Produk</th>
                        <th class="px-6 py-3 text-right">Harga Beli</th>
                        <th class="px-6 py-3 text-center w-48">Qty (Diterima / Pesan)</th>
                        <th class="px-6 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($purchase->details as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">{{ $item->product->name }}</div>
                            <div class="text-xs text-slate-500">{{ $item->product->sku }}</div>
                        </td>
                        <td class="px-6 py-4 text-right font-mono text-slate-600">
                            Rp {{ number_format($item->buy_price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center align-middle">
                            {{-- PROGRESS BAR PENERIMAAN --}}
                            <div class="flex justify-center items-center gap-1 mb-1 font-bold text-slate-700">
                                <span class="{{ $item->quantity_received >= $item->quantity ? 'text-green-600' : 'text-orange-600' }}">
                                    {{ $item->quantity_received }}
                                </span>
                                <span class="text-slate-400 text-xs">/</span>
                                <span>{{ $item->quantity }}</span>
                                <span class="text-xs text-slate-400 font-normal">{{ $item->product->unit }}</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                @php
                                    $percent = $item->quantity > 0 ? ($item->quantity_received / $item->quantity) * 100 : 0;
                                    $color = $percent >= 100 ? 'bg-green-500' : 'bg-orange-500';
                                @endphp
                                <div class="{{ $color }} h-1.5 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right font-mono font-bold text-slate-800">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 border-t border-slate-200">
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-bold text-slate-600 uppercase text-xs">Total Amount</td>
                        <td class="px-6 py-3 text-right font-mono font-bold text-lg text-indigo-700">
                            Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- HISTORY PEMBAYARAN (TRACKING) --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-8">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                <h3 class="font-bold text-slate-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Riwayat Pembayaran (Payment History)
                </h3>
            </div>
            @if($purchase->payments->count() > 0)
                <table class="w-full text-sm text-left">
                    <thead class="bg-white text-slate-500 font-semibold border-b border-slate-100 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Metode</th>
                            <th class="px-6 py-3">Input Oleh</th>
                            <th class="px-6 py-3 text-right">Nominal</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($purchase->payments as $payment)
                        <tr>
                            <td class="px-6 py-3 text-slate-600">{{ date('d/m/Y', strtotime($payment->date)) }}</td>
                            <td class="px-6 py-3">
                                <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs border border-slate-200 font-medium">
                                    {{ $payment->payment_method }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-slate-500 text-xs">{{ $payment->user->name }}</td>
                            <td class="px-6 py-3 text-right font-mono font-bold text-green-600">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3 text-center">
                                <a href="{{ route('purchases.payments.print', $payment->id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold border border-indigo-200 bg-indigo-50 px-2 py-1 rounded flex items-center justify-center gap-1 w-fit mx-auto transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Voucher
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-8 text-center text-slate-400 text-sm">
                    Belum ada pembayaran untuk PO ini.
                </div>
            @endif
        </div>

        {{-- ACTION BAR --}}
        <div class="flex flex-col md:flex-row justify-between items-center bg-white p-6 rounded-xl border border-slate-200 shadow-sm gap-4">
            
            {{-- KIRI: CANCEL / DELETE --}}
            <div>
                @php
                    $hasReceived = $purchase->details->sum('quantity_received') > 0;
                @endphp

                @if($purchase->status != 'canceled' && !$hasReceived && $purchase->status != 'completed')
                    <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" onsubmit="return confirm('Yakin ingin MEMBATALKAN PO ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-bold text-sm flex items-center gap-1 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Batalkan PO
                        </button>
                    </form>
                @endif
            </div>

            {{-- KANAN: ACTIONS --}}
            <div class="flex flex-wrap gap-3 justify-end">
                
                {{-- 1. CETAK PDF --}}
                <a href="{{ route('purchases.print', $purchase->id) }}" target="_blank" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 font-bold text-sm flex items-center gap-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak PO
                </a>

                {{-- 2. EDIT (Hanya jika belum ada barang masuk) --}}
                @if(!$hasReceived && $purchase->status != 'canceled')
                    <a href="{{ route('purchases.edit', $purchase->id) }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 font-bold text-sm transition-colors">
                        ✏️ Edit Data
                    </a>
                @endif

                {{-- 3. TERIMA BARANG (Muncul jika belum Completed & tidak Canceled) --}}
                @if($purchase->status != 'completed' && $purchase->status != 'canceled')
                    <a href="{{ route('purchases.receive', $purchase->id) }}" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-sm shadow hover:shadow-md transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Terima Barang
                    </a>
                @endif

                {{-- 4. BAYAR HUTANG (Muncul jika belum Lunas & tidak Canceled) --}}
                @if($purchase->payment_status != 'paid' && $purchase->status != 'canceled')
                    <a href="{{ route('purchases.pay', $purchase->id) }}" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold text-sm shadow hover:shadow-md transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Bayar Hutang
                    </a>
                @endif

            </div>
        </div>

    </div>
</x-app-layout>