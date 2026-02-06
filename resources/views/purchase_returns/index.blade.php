<x-app-layout>
    <div class="max-w-[1920px] mx-auto space-y-6">
        
        {{-- HEADER & STATS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Judul & Tombol --}}
            <div class="md:col-span-1">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Retur Pembelian</h1>
                <p class="text-slate-500 text-sm">Kelola pengembalian barang ke Supplier.</p>
                
                <a href="{{ route('purchase_returns.create') }}" class="mt-4 inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-md transition-all w-full md:w-auto justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Buat Retur Baru
                </a>
            </div>

            {{-- Kartu Statistik 1 --}}
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase">Menunggu Persetujuan</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['pending_count'] }}</p>
                </div>
                <div class="p-2 bg-orange-50 rounded-lg text-orange-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            {{-- Kartu Statistik 2 --}}
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase">Retur Hari Ini</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['today_count'] }}</p>
                </div>
                <div class="p-2 bg-red-50 rounded-lg text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4">
            <form method="GET" action="{{ route('purchase_returns.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    
                    {{-- 1. Search --}}
                    <div class="lg:col-span-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Retur, PO, atau Supplier..." 
                            class="pl-10 w-full rounded-lg border-slate-300 text-sm focus:ring-red-500 focus:border-red-500">
                    </div>

                    {{-- 2. Status Filter --}}
                    <div>
                        <select name="status" class="w-full rounded-lg border-slate-300 text-sm focus:ring-red-500 focus:border-red-500">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Disetujui</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Ditolak</option>
                        </select>
                    </div>

                    {{-- 3. Filter Tanggal Mulai --}}
                    <div>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-slate-300 text-sm focus:ring-red-500 focus:border-red-500" placeholder="Dari Tanggal">
                    </div>

                    {{-- 4. Tombol Aksi --}}
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors shadow-sm">Filter</button>
                        <a href="{{ route('purchase_returns.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 text-sm font-medium">Reset</a>
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
                            <th class="px-6 py-4">No. Retur / Tanggal</th>
                            <th class="px-6 py-4">Supplier & Ref PO</th>
                            <th class="px-6 py-4">Alasan Retur</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($returns as $return)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            
                            {{-- KOLOM 1: NO RETUR --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-red-600 text-base font-mono">
                                        RET-{{ str_pad($return->id, 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span class="text-xs text-slate-500 flex items-center gap-1 mt-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ date('d M Y', strtotime($return->date)) }}
                                    </span>
                                </div>
                            </td>

                            {{-- KOLOM 2: SUPPLIER & PO --}}
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $return->purchase->supplier->name ?? 'Unknown' }}</div>
                                <div class="mt-1">
                                    <a href="{{ route('purchases.show', $return->purchase_id) }}" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-600 px-2 py-0.5 rounded border border-slate-200 transition-colors">
                                        Ref PO: {{ $return->purchase->po_number }}
                                    </a>
                                </div>
                            </td>

                            {{-- KOLOM 3: ALASAN --}}
                            <td class="px-6 py-4 max-w-xs">
                                <div class="text-slate-600 text-sm truncate" title="{{ $return->reason }}">
                                    {{ Str::limit($return->reason, 50) }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-1 italic">
                                    Operator: {{ $return->user->name ?? 'System' }}
                                </div>
                            </td>

                            {{-- KOLOM 4: STATUS --}}
                            <td class="px-6 py-4 text-center">
                                @if($return->status == 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        ⏳ PENDING
                                    </span>
                                @elseif($return->status == 'approved')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-800 border border-green-200">
                                        ✅ DISETUJUI
                                    </span>
                                @elseif($return->status == 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-800 border border-red-200">
                                        ❌ DITOLAK
                                    </span>
                                @endif
                            </td>

                            {{-- KOLOM 5: AKSI --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Lihat Detail --}}
                                    <a href="{{ route('purchase_returns.show', $return->id) }}" class="p-2 bg-white border border-slate-200 text-slate-500 rounded-lg hover:text-indigo-600 hover:border-indigo-300 transition-colors" title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>

                                    {{-- Cetak (Jika Approved) --}}
                                    @if($return->status == 'approved')
                                        <a href="{{ route('purchase_returns.print', $return->id) }}" target="_blank" class="p-2 bg-white border border-slate-200 text-slate-500 rounded-lg hover:text-red-600 hover:border-red-300 transition-colors" title="Cetak Nota">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center bg-slate-50">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-white p-3 rounded-full shadow-sm mb-3">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                    </div>
                                    <p class="font-medium text-slate-900">Tidak ada data retur.</p>
                                    <p class="text-slate-500 text-sm mt-1">Gunakan filter atau buat retur baru.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- PAGINATION --}}
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $returns->links() }}
            </div>
        </div>
    </div>
</x-app-layout>