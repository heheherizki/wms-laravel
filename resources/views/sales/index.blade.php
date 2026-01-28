<x-app-layout>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Daftar Penjualan (SO)</h1>
            <p class="text-sm text-slate-500">Sales Orders, Status Pengiriman & Pembayaran</p>
        </div>
        <a href="{{ route('sales.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-md transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Order Baru
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 shadow-sm rounded-r relative flex items-center gap-2" role="alert">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="block sm:inline font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-4">No. SO / Tanggal</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4 text-right">Total Nilai</th>
                        <th class="px-6 py-4 text-center">Status Order</th>
                        <th class="px-6 py-4 text-center">Status Pembayaran</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        
                        {{-- 1. NO SO & TANGGAL --}}
                        <td class="px-6 py-4">
                            <div class="font-bold text-indigo-600 font-mono group-hover:text-indigo-700 transition-colors">{{ $order->so_number }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">{{ date('d/m/Y', strtotime($order->date)) }}</div>
                        </td>

                        {{-- 2. CUSTOMER & TIMER UNLOCKED --}}
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">{{ $order->customer->name }}</div>
                            <div class="text-xs text-slate-400">{{ $order->user->name ?? 'Admin' }}</div>

                            {{-- INDIKATOR TIMER BERGERAK --}}
                            @if($order->customer->authorized_until && now()->lt($order->customer->authorized_until))
                                @php
                                    // Hitung Detik Bulat
                                    $sisaDetik = (int) now()->diffInSeconds($order->customer->authorized_until, false);
                                @endphp
                                
                                {{-- Alpine Component untuk setiap baris --}}
                                <div x-data="timer({{ $sisaDetik }})" x-init="start()" class="mt-2 inline-flex items-center gap-1.5 px-2 py-1 rounded bg-indigo-50 border border-indigo-200 text-indigo-700 text-[10px] font-bold shadow-sm">
                                    <span class="relative flex h-2 w-2">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                                    </span>
                                    UNLOCKED (<span x-text="formatTime()"></span>)
                                </div>
                            @endif
                        </td>

                        {{-- 3. TOTAL NILAI --}}
                        <td class="px-6 py-4 text-right font-mono font-bold text-slate-700">
                            Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                        </td>

                        {{-- 4. STATUS ORDER (LOGISTIK) --}}
                        <td class="px-6 py-4 text-center">
                            @if($order->status == 'pending')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">
                                    Pending
                                </span>
                            @elseif($order->status == 'on_hold')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200 animate-pulse shadow-sm">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    ON HOLD
                                </span>
                            @elseif($order->status == 'partial')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                    Partial Ship
                                </span>
                            @elseif($order->status == 'shipped')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    Shipped
                                </span>
                            @elseif($order->status == 'canceled')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                    Batal
                                </span>
                            @endif
                        </td>

                        {{-- 5. STATUS PEMBAYARAN --}}
                        <td class="px-6 py-4 text-center">
                            @if($order->payment_status == 'paid')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    LUNAS
                                </span>
                            @elseif($order->payment_status == 'partial')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-orange-50 text-orange-700 border border-orange-100">
                                    Sebagian
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-100">
                                    Belum Lunas
                                </span>
                            @endif
                        </td>

                        {{-- 6. AKSI --}}
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('sales.show', $order->id) }}" class="inline-flex items-center gap-1 text-slate-500 hover:text-indigo-600 font-bold text-xs border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50 px-3 py-1.5 rounded-lg transition-all">
                                Detail
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 bg-slate-50">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="font-medium">Belum ada data penjualan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $orders->links() }}
        </div>
    </div>

    {{-- SCRIPT GLOBAL TIMER (Bisa Dipanggil Berkali-kali) --}}
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
</x-app-layout>