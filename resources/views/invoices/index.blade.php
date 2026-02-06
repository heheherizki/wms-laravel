<x-app-layout>
    <div class="max-w-[1920px] mx-auto space-y-6">
        
        {{-- HEADER & STATS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Judul --}}
            <div class="md:col-span-1">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Invoice & Piutang</h1>
                <p class="text-slate-500 text-sm">Kelola tagihan pelanggan dan arus kas masuk.</p>
                <div class="mt-4 text-xs text-slate-400">
                    *Invoice dibuat otomatis saat Shipment (Surat Jalan) diterbitkan.
                </div>
            </div>

            {{-- Kartu Statistik 1 --}}
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase">Total Piutang (Receivable)</p>
                    <p class="text-2xl font-bold text-indigo-600">Rp {{ number_format($stats['total_receivable'], 0, ',', '.') }}</p>
                </div>
                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            {{-- Kartu Statistik 2 --}}
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div class="flex gap-8">
                    <div>
                        <p class="text-xs text-slate-500 font-bold uppercase">Belum Lunas</p>
                        <p class="text-xl font-bold text-orange-600">{{ $stats['unpaid_count'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-bold uppercase text-red-600">Jatuh Tempo</p>
                        <p class="text-xl font-bold text-red-600">{{ $stats['overdue_count'] }}</p>
                    </div>
                </div>
                <div class="p-2 bg-red-50 rounded-lg text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4">
            <form method="GET" action="{{ route('invoices.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    
                    {{-- 1. Search --}}
                    <div class="lg:col-span-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Invoice, Customer, atau Shipment..." 
                            class="pl-10 w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    {{-- 2. Status Filter --}}
                    <div>
                        <select name="status" class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Status Bayar</option>
                            <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>❌ Belum Lunas</option>
                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>⚠️ Partial (Cicil)</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>✅ Lunas</option>
                        </select>
                    </div>

                    {{-- 3. Filter Jatuh Tempo --}}
                    <div>
                        <input type="date" name="due_date_end" value="{{ request('due_date_end') }}" class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Jatuh Tempo Sebelum">
                    </div>

                    {{-- 4. Tombol Aksi --}}
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors shadow-sm">Filter</button>
                        <a href="{{ route('invoices.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 text-sm font-medium">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABEL DATA --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="px-6 py-4">No. Invoice / Terbit</th>
                            <th class="px-6 py-4">Customer & Ref</th>
                            <th class="px-6 py-4 text-right">Total Tagihan</th>
                            <th class="px-6 py-4 text-right">Sisa Tagihan</th>
                            <th class="px-6 py-4 text-center">Jatuh Tempo</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            
                            {{-- KOLOM 1: NO INVOICE --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <a href="{{ route('invoices.show', $inv->id) }}" class="font-bold text-indigo-600 text-base font-mono hover:underline hover:text-indigo-800">
                                        {{ $inv->invoice_number }}
                                    </a>
                                    <span class="text-xs text-slate-500 mt-1">
                                        {{ date('d M Y', strtotime($inv->created_at)) }}
                                    </span>
                                </div>
                            </td>

                            {{-- KOLOM 2: CUSTOMER --}}
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $inv->salesOrder->customer->name ?? 'Unknown' }}</div>
                                <div class="flex gap-2 mt-1">
                                    <span class="text-[10px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded border border-slate-200">
                                        Ref: {{ $inv->shipment->shipment_number }}
                                    </span>
                                </div>
                            </td>

                            {{-- KOLOM 3: TOTAL --}}
                            <td class="px-6 py-4 text-right">
                                <div class="font-mono text-slate-700 font-bold">
                                    Rp {{ number_format($inv->total_amount, 0, ',', '.') }}
                                </div>
                            </td>

                            {{-- KOLOM 4: SISA (BALANCE) --}}
                            <td class="px-6 py-4 text-right">
                                @if($inv->remaining_balance > 0)
                                    <div class="font-mono text-red-600 font-bold">
                                        Rp {{ number_format($inv->remaining_balance, 0, ',', '.') }}
                                    </div>
                                @else
                                    <span class="text-slate-400 font-mono text-xs">-</span>
                                @endif
                            </td>

                            {{-- KOLOM 5: JATUH TEMPO --}}
                            <td class="px-6 py-4 text-center">
                                @php
                                    $isOverdue = $inv->status != 'paid' && \Carbon\Carbon::parse($inv->due_date)->isPast();
                                @endphp
                                <div class="{{ $isOverdue ? 'text-red-600 font-bold animate-pulse' : 'text-slate-600' }}">
                                    {{ date('d M Y', strtotime($inv->due_date)) }}
                                </div>
                                @if($isOverdue)
                                    <span class="text-[10px] text-red-500 font-bold block">TERLAMBAT!</span>
                                @endif
                            </td>

                            {{-- KOLOM 6: STATUS --}}
                            <td class="px-6 py-4 text-center">
                                @if($inv->status == 'paid')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-800 border border-green-200">
                                        LUNAS
                                    </span>
                                @elseif($inv->status == 'partial')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-orange-100 text-orange-800 border border-orange-200">
                                        CICILAN
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200">
                                        BELUM LUNAS
                                    </span>
                                @endif
                            </td>

                            {{-- KOLOM 7: AKSI --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Lihat/Bayar --}}
                                    <a href="{{ route('invoices.show', $inv->id) }}" class="p-2 bg-white border border-slate-200 text-slate-500 rounded-lg hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Lihat Detail & Bayar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </a>

                                    {{-- Cetak --}}
                                    <a href="{{ route('invoices.print', $inv->id) }}" target="_blank" class="p-2 bg-white border border-slate-200 text-slate-500 rounded-lg hover:text-slate-800 hover:bg-slate-100 transition-colors" title="Cetak Faktur">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center bg-slate-50">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-white p-3 rounded-full shadow-sm mb-3">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <p class="font-medium text-slate-900">Belum ada invoice diterbitkan.</p>
                                    <p class="text-slate-500 text-sm mt-1">Invoice muncul otomatis setelah Shipment (Pengiriman) dibuat.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- PAGINATION --}}
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</x-app-layout>