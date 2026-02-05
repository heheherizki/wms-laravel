<x-app-layout>
    <div class="max-w-[1920px] mx-auto space-y-6">

        {{-- HEADER & STATISTIK RINGKAS --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Judul --}}
            <div class="md:col-span-1">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Penjualan (SO)</h1>
                <p class="text-slate-500 text-sm">Monitor order dan pengiriman.</p>
                
                @can('create_sales')
                <a href="{{ route('sales.create') }}" class="mt-4 inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-md transition-all w-full md:w-auto justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Buat Order Baru
                </a>
                @endcan
            </div>

            {{-- Kartu Statistik Kecil --}}
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase">Order Hari Ini</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['today_count'] }}</p>
                </div>
                <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase">Perlu Dikirim</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['pending_ship'] }}</p>
                </div>
                <div class="p-2 bg-orange-50 rounded-lg text-orange-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase">Belum Lunas</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['unpaid_count'] }}</p>
                </div>
                <div class="p-2 bg-red-50 rounded-lg text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        {{-- FILTER SECTION --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4">
            <form method="GET" action="{{ route('sales.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    
                    {{-- 1. Search Text --}}
                    <div class="lg:col-span-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. SO atau Nama Customer..." 
                            class="pl-10 w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    {{-- 2. Filter Status Order (Multi-Select FIX) --}}
                    <div x-data='{ 
                        open: false, 
                        selected: @json((array)request("status", [])),
                        labels: {
                            "pending": "Pending",
                            "partial": "Partial",
                            "shipped": "Shipped",
                            "on_hold": "On Hold",
                            "canceled": "Batal"
                        },
                        toggle(value) {
                            if (this.selected.includes(value)) {
                                this.selected = this.selected.filter(item => item !== value);
                            } else {
                                this.selected.push(value);
                            }
                        },
                        displayText() {
                            if (this.selected.length === 0) return "Semua Status";
                            if (this.selected.length <= 2) {
                                return this.selected.map(s => this.labels[s]).join(", ");
                            }
                            return this.selected.length + " Status Dipilih";
                        }
                    }' class="relative">
                        
                        {{-- Input Hidden --}}
                        <template x-for="status in selected">
                            <input type="hidden" name="status[]" :value="status">
                        </template>

                        {{-- Tombol --}}
                        <button @click="open = !open" @click.away="open = false" type="button" 
                            class="w-full bg-white border border-slate-300 text-slate-700 py-2 px-3 rounded-lg text-sm text-left flex justify-between items-center shadow-sm focus:ring-2 focus:ring-indigo-500">
                            <span x-text="displayText()" class="truncate block mr-2"></span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="open" x-cloak class="absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-xl p-2 max-h-60 overflow-y-auto">
                            <div class="space-y-1">
                                <label class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded cursor-pointer">
                                    <input type="checkbox" value="pending" :checked="selected.includes('pending')" @change="toggle('pending')" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm text-slate-700">⏳ Pending</span>
                                </label>
                                <label class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded cursor-pointer">
                                    <input type="checkbox" value="partial" :checked="selected.includes('partial')" @change="toggle('partial')" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm text-slate-700">📦 Partial Ship</span>
                                </label>
                                <label class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded cursor-pointer">
                                    <input type="checkbox" value="shipped" :checked="selected.includes('shipped')" @change="toggle('shipped')" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm text-slate-700">✅ Shipped</span>
                                </label>
                                <label class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded cursor-pointer">
                                    <input type="checkbox" value="on_hold" :checked="selected.includes('on_hold')" @change="toggle('on_hold')" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm text-slate-700">✋ On Hold</span>
                                </label>
                                <label class="flex items-center gap-2 p-2 hover:bg-slate-50 rounded cursor-pointer">
                                    <input type="checkbox" value="canceled" :checked="selected.includes('canceled')" @change="toggle('canceled')" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm text-slate-700">❌ Batal</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Filter Status Bayar --}}
                    <div>
                        <select name="payment_status" class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Pembayaran</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Lunas</option>
                            <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Sebagian</option>
                            <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum Bayar</option>
                        </select>
                    </div>

                    {{-- 4. Tombol Filter --}}
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors shadow-sm">
                            Filter
                        </button>
                        <a href="{{ route('sales.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 text-sm font-medium">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABEL DATA --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="px-6 py-4 w-40">No. SO / Tanggal</th>
                            <th class="px-6 py-4">Customer</th>
                            <th class="px-6 py-4 text-right">Total Nilai</th>
                            <th class="px-6 py-4 text-center">Logistik</th>
                            <th class="px-6 py-4 text-center">Keuangan</th>
                            <th class="px-6 py-4 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($orders as $order)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            
                            {{-- No SO --}}
                            <td class="px-6 py-4">
                                <div class="font-bold text-indigo-600 font-mono group-hover:text-indigo-800 transition-colors">
                                    <a href="{{ route('sales.show', $order->id) }}">{{ $order->so_number }}</a>
                                </div>
                                <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ date('d M Y', strtotime($order->date)) }}
                                </div>
                            </td>

                            {{-- Customer --}}
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $order->customer->name }}</div>
                                <div class="text-xs text-slate-400">Sales: {{ $order->user->name ?? 'Admin' }}</div>

                                {{-- Timer Unlocked --}}
                                @if($order->customer->authorized_until && now()->lt($order->customer->authorized_until))
                                    @php
                                        $sisaDetik = (int) now()->diffInSeconds($order->customer->authorized_until, false);
                                    @endphp
                                    <div x-data="timer({{ $sisaDetik }})" x-init="start()" class="mt-2 inline-flex items-center gap-1.5 px-2 py-1 rounded bg-yellow-50 border border-yellow-200 text-yellow-700 text-[10px] font-bold shadow-sm animate-pulse">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        UNLOCKED (<span x-text="formatTime()"></span>)
                                    </div>
                                @endif
                            </td>

                            {{-- Total --}}
                            <td class="px-6 py-4 text-right">
                                <div class="font-mono font-bold text-slate-700">
                                    Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                                </div>
                            </td>

                            {{-- Status Order --}}
                            <td class="px-6 py-4 text-center">
                                @if($order->status == 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        ⏳ Pending
                                    </span>
                                @elseif($order->status == 'on_hold')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">
                                        ✋ On Hold
                                    </span>
                                @elseif($order->status == 'partial')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                        📦 Partial
                                    </span>
                                @elseif($order->status == 'shipped')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                        ✅ Dikirim
                                    </span>
                                @elseif($order->status == 'canceled')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                        ❌ Batal
                                    </span>
                                @endif
                            </td>

                            {{-- Status Pembayaran --}}
                            <td class="px-6 py-4 text-center">
                                @if($order->payment_status == 'paid')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        LUNAS
                                    </span>
                                @elseif($order->payment_status == 'partial')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-100">
                                        CICIL
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-50 text-red-600 border border-red-100">
                                        BELUM
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('sales.show', $order->id) }}" class="p-2 bg-white border border-slate-200 text-slate-500 rounded-lg hover:text-indigo-600 hover:border-indigo-300 transition-colors inline-block" title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center bg-slate-50">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-white p-3 rounded-full shadow-sm mb-3">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    </div>
                                    <p class="font-medium text-slate-900">Tidak ada data ditemukan.</p>
                                    <p class="text-slate-500 text-sm mt-1">Coba ubah filter pencarian Anda.</p>
                                    <a href="{{ route('sales.index') }}" class="text-indigo-600 hover:underline text-sm mt-2">Reset Filter</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $orders->links() }}
            </div>
        </div>
    </div>

    {{-- SCRIPT TIMER --}}
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