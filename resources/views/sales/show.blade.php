<x-app-layout>
    <div class="max-w-6xl mx-auto">
        
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('sales.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:border-indigo-600 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 flex items-center gap-2">
                        Detail Sales Order
                        <span class="text-base font-normal text-slate-400 font-mono bg-slate-100 px-2 py-0.5 rounded border border-slate-200">{{ $order->so_number }}</span>
                    </h1>
                    <div class="flex items-center gap-2 text-sm text-slate-500 mt-0.5">
                        <span>Dibuat pada: {{ date('d F Y', strtotime($order->date)) }}</span>
                        <span>&bull;</span>
                        <span>Oleh: {{ $order->user->name }}</span>
                    </div>
                </div>
            </div>
            
            <a href="{{ route('sales.print_so', $order->id) }}" target="_blank" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold text-sm rounded-lg shadow-sm flex items-center gap-2 transition-all">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print Picking List
            </a>
        </div>

        @if($order->customer->authorized_until && now()->lt($order->customer->authorized_until))
            
            {{-- HITUNG MUNDUR (BERBASIS CUSTOMER) --}}
            @php
                $sisaDetik = (int) now()->diffInSeconds($order->customer->authorized_until, false);
            @endphp

            <div x-data="timer({{ $sisaDetik }})" x-init="start()" class="mb-6 bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded-r-xl shadow-sm flex items-start gap-3">
                
                <div class="p-2 bg-indigo-100 text-indigo-600 rounded-full animate-pulse flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                
                <div class="flex-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-indigo-800">AUTHORIZED / RELEASED BY ADMIN</h3>
                            <p class="text-xs text-indigo-500 mt-1">Admin telah membuka blokir kredit untuk Customer ini.</p>
                        </div>
                        
                        {{-- KOTAK WAKTU --}}
                        <div class="px-4 py-2 bg-white border border-indigo-200 rounded-lg shadow-sm text-center min-w-[110px]">
                            <span class="text-[10px] text-indigo-400 uppercase font-bold tracking-wider block mb-1">Sisa Waktu</span>
                            <span class="text-2xl font-mono font-bold text-indigo-700 tracking-widest" x-text="formatTime()">
                                --:--
                            </span>
                        </div>
                    </div>

                    <p class="text-sm text-indigo-700 mt-2">
                        Semua order milik <strong>{{ $order->customer->name }}</strong> dapat diproses hingga pukul: 
                        <strong>{{ $order->customer->authorized_until->translatedFormat('H:i') }} WIB</strong>.
                    </p>
                </div>
            </div>

            <script>
                function timer(initialSeconds) {
                    return {
                        timeLeft: initialSeconds,
                        interval: null,
                        start() {
                            this.interval = setInterval(() => {
                                if (this.timeLeft > 0) this.timeLeft--;
                                else clearInterval(this.interval);
                            }, 1000);
                        },
                        formatTime() {
                            const minutes = Math.floor(this.timeLeft / 60);
                            const seconds = this.timeLeft % 60;
                            return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                        }
                    }
                }
            </script>
        @endif

        @if($order->status == 'on_hold')
            @php
                // --- LOGIC PENGECEKAN REAL-TIME (REVISI AKURAT) ---
                
                // 1. Ambil Hutang Real (Invoice Unpaid)
                $realDebt = $order->customer->current_debt; 
                
                // 2. Ambil Total Order Gantung (Pending + On Hold)
                // Kita harus hitung semua order milik customer ini, BUKAN CUMA ORDER INI.
                $pendingOrdersExposure = $order->customer->salesOrders()
                                            ->whereIn('status', ['pending', 'on_hold'])
                                            ->where('payment_status', '!=', 'paid') 
                                            ->sum('grand_total');

                // Total Resiko = Hutang Invoice + Semua Order Gantung
                $totalExposure = $realDebt + $pendingOrdersExposure;
                $limit = $order->customer->credit_limit;
                
                // Cek Masalah
                $isOverLimit = $limit > 0 && ($totalExposure > $limit);
                $isOverdue = $order->customer->hasOverdueInvoices();
                
                // Ghost Hold = Status ON HOLD, tapi hitungan aman
                $isGhostHold = !$isOverLimit && !$isOverdue;
            @endphp

            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-6 rounded-r-xl shadow-sm relative overflow-hidden">
                <div class="flex items-start gap-4 relative z-10">
                    
                    {{-- Ikon Berubah sesuai kondisi --}}
                    <div class="p-3 {{ $isGhostHold ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }} rounded-full shrink-0 animate-pulse">
                        @if($isGhostHold)
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        @else
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        @endif
                    </div>

                    <div class="flex-1">
                        @if($isGhostHold)
                            <h3 class="text-lg font-bold text-green-800">STATUS KEUANGAN SUDAH AMAN</h3>
                            <p class="text-green-700 mt-1 text-sm">
                                Sistem mendeteksi Limit Cukup dan Tidak Ada Overdue. <br>
                                Status "On Hold" saat ini mungkin tersisa dari pengecekan sebelumnya.
                            </p>
                            <div class="mt-4">
                                <form action="{{ route('sales.refresh', $order->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-md transition-colors flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Update Status Order ke Pending
                                    </button>
                                </form>
                            </div>
                        @else
                            <h3 class="text-lg font-bold text-red-800">ORDER DITAHAN (CREDIT HOLD)</h3>
                            <p class="text-red-700 mt-1 text-sm">Pesanan ini terkunci oleh sistem karena masalah kredit. Hubungi bagian keuangan.</p>
                            
                            <div class="mt-4 flex flex-wrap gap-2">
                                @if($isOverLimit)
                                    <div class="bg-white/80 border border-red-200 px-3 py-2 rounded-lg text-sm text-red-700 flex items-center gap-2">
                                        <span class="font-bold">⛔ Over Limit:</span>
                                        <span>Total Eksposur (Rp {{ number_format($totalExposure, 0,',','.') }}) > Limit (Rp {{ number_format($limit, 0,',','.') }})</span>
                                    </div>
                                @endif

                                @if($isOverdue)
                                    <div class="bg-white/80 border border-red-200 px-3 py-2 rounded-lg text-sm text-red-700 flex items-center gap-2">
                                        <span class="font-bold">⏳ Overdue Invoice:</span>
                                        <span>Ada tagihan jatuh tempo yang belum lunas.</span>
                                    </div>
                                @endif
                            </div>

                            @if(Auth::user()->role == 'admin')
                                <div class="mt-4 pt-4 border-t border-red-200">
                                    <a href="{{ route('customers.index') }}" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        Buka Menu Customer untuk Unlock
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            
            <div class="lg:col-span-2 space-y-6">
                
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">Informasi Customer</h3>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-xl">
                            {{ substr($order->customer->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="text-lg font-bold text-slate-900">{{ $order->customer->name }}</div>
                            <div class="text-slate-500 text-sm mt-1">{{ $order->customer->address ?? 'Alamat tidak tersedia' }}</div>
                            <div class="flex gap-4 mt-3 text-sm">
                                <span class="flex items-center gap-1 text-slate-600">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    {{ $order->customer->phone ?? '-' }}
                                </span>
                                <span class="flex items-center gap-1 text-slate-600">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    {{ $order->customer->email ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="font-bold text-slate-800">Daftar Barang</h3>
                        <span class="bg-white border border-slate-200 px-2 py-0.5 rounded text-xs font-bold text-slate-500">{{ $order->details->count() }} Item</span>
                    </div>
                    <table class="w-full text-sm text-left">
                        <thead class="bg-white text-slate-500 font-semibold border-b border-slate-200">
                            <tr>
                                <th class="pl-6 py-3">Produk</th>
                                <th class="px-4 py-3 text-center">Qty</th>
                                <th class="px-4 py-3 text-right">Harga</th>
                                <th class="pr-6 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($order->details as $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="pl-6 py-4">
                                    <div class="font-bold text-slate-800">{{ $item->product->name }}</div>
                                    <div class="text-xs text-slate-500 font-mono">{{ $item->product->sku }}</div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="bg-slate-100 text-slate-700 px-2 py-1 rounded font-bold text-xs">{{ $item->quantity }} {{ $item->product->unit }}</span>
                                </td>
                                <td class="px-4 py-4 text-right font-mono text-slate-600">
                                    {{ number_format($item->price, 0, ',', '.') }}
                                </td>
                                <td class="pr-6 py-4 text-right font-mono font-bold text-slate-800">
                                    {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t border-slate-200">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right font-bold text-slate-600">Grand Total</td>
                                <td class="px-6 py-4 text-right font-mono font-bold text-xl text-indigo-600">
                                    Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($order->shipments->count() > 0)
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                        <h3 class="font-bold text-slate-800">Riwayat Pengiriman</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @foreach($order->shipments as $sj)
                        <div class="p-4 flex items-center justify-between hover:bg-slate-50">
                            <div class="flex items-center gap-4">
                                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">{{ $sj->shipment_number }}</div>
                                    <div class="text-xs text-slate-500">{{ date('d M Y', strtotime($sj->date)) }} &bull; {{ $sj->notes ?? 'Tanpa catatan' }}</div>
                                </div>
                            </div>
                            <a href="{{ route('shipments.print', $sj->id) }}" target="_blank" class="text-sm font-bold text-blue-600 hover:underline">
                                Cetak SJ
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
                
            </div>

            <div class="space-y-6">
                
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Status Pesanan</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="text-sm text-slate-500 mb-1">Status Logistik</div>
                            @if($order->status == 'pending')
                                <span class="block text-center w-full py-2 rounded-lg bg-yellow-100 text-yellow-800 font-bold border border-yellow-200">Pending</span>
                            @elseif($order->status == 'on_hold')
                                <span class="block text-center w-full py-2 rounded-lg bg-red-100 text-red-800 font-bold border border-red-200">On Hold</span>
                            @elseif($order->status == 'partial')
                                <span class="block text-center w-full py-2 rounded-lg bg-blue-100 text-blue-800 font-bold border border-blue-200">Partial Ship</span>
                            @elseif($order->status == 'shipped')
                                <span class="block text-center w-full py-2 rounded-lg bg-green-100 text-green-800 font-bold border border-green-200">Shipped</span>
                            @elseif($order->status == 'canceled')
                                <span class="block text-center w-full py-2 rounded-lg bg-slate-100 text-slate-800 font-bold border border-slate-200">Canceled</span>
                            @endif
                        </div>

                        <div>
                            <div class="text-sm text-slate-500 mb-1">Status Pembayaran</div>
                            @if($order->payment_status == 'paid')
                                <span class="block text-center w-full py-2 rounded-lg bg-emerald-100 text-emerald-800 font-bold border border-emerald-200">Lunas (Paid)</span>
                            @elseif($order->payment_status == 'partial')
                                <span class="block text-center w-full py-2 rounded-lg bg-orange-100 text-orange-800 font-bold border border-orange-200">Sebagian (Partial)</span>
                            @else
                                <span class="block text-center w-full py-2 rounded-lg bg-slate-100 text-slate-600 font-bold border border-slate-200">Belum Lunas (Unpaid)</span>
                            @endif
                        </div>

                        @if($order->notes)
                        <div class="pt-4 border-t border-slate-100">
                            <div class="text-sm text-slate-500 mb-1">Catatan Internal</div>
                            <p class="text-sm text-slate-700 italic bg-slate-50 p-3 rounded border border-slate-100">
                                "{{ $order->notes }}"
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Aksi Cepat</h3>
                    
                    <div class="space-y-3">
                        @if($order->status != 'shipped' && $order->status != 'canceled' && $order->status != 'on_hold')
                            <a href="{{ route('shipments.create', $order->id) }}" class="w-full block text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-md transition-all transform hover:-translate-y-0.5">
                                Proses Pengiriman
                            </a>
                        @elseif($order->status == 'on_hold')
                            <button disabled class="w-full block text-center bg-slate-100 text-slate-400 font-bold py-3 rounded-lg border border-slate-200 cursor-not-allowed">
                                Pengiriman Terkunci
                            </button>
                        @endif

                        @if($order->status == 'pending')
                            <form action="{{ route('sales.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full block text-center bg-white border border-red-200 text-red-600 hover:bg-red-50 font-bold py-2.5 rounded-lg transition-colors">
                                    Batalkan Pesanan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>