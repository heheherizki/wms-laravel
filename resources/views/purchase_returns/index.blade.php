<x-app-layout>
    <div class="max-w-7xl mx-auto">
        
        {{-- HEADER SECTION --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Retur Pembelian</h1>
                <p class="text-slate-500 mt-1">Kelola pengembalian barang ke Supplier (Debit Note).</p>
            </div>
            <a href="{{ route('purchase_returns.create') }}" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Buat Retur Baru
            </a>
        </div>

        {{-- FLASH MESSAGES --}}
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 uppercase text-xs tracking-wider">
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
                            
                            {{-- KOLOM 1: ID & TANGGAL --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-800 text-base font-mono">
                                        RET-{{ str_pad($return->id, 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <span class="text-xs text-slate-500 flex items-center gap-1 mt-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ date('d M Y', strtotime($return->date)) }}
                                    </span>
                                </div>
                            </td>

                            {{-- KOLOM 2: SUPPLIER --}}
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-700">{{ $return->purchase->supplier->name }}</div>
                                <div class="text-xs text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded w-fit mt-1 font-medium border border-indigo-100">
                                    Ref PO: {{ $return->purchase->po_number }}
                                </div>
                            </td>

                            {{-- KOLOM 3: ALASAN (TRUNCATED) --}}
                            <td class="px-6 py-4 max-w-xs">
                                <p class="text-slate-600 truncate" title="{{ $return->reason }}">
                                    {{ Str::limit($return->reason, 40) }}
                                </p>
                                <div class="text-xs text-slate-400 mt-1">
                                    Oleh: {{ $return->user->name }}
                                </div>
                            </td>

                            {{-- KOLOM 4: STATUS --}}
                            <td class="px-6 py-4 text-center">
                                @if($return->status == 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        PENDING
                                    </span>
                                @elseif($return->status == 'approved')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        APPROVED
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        REJECTED
                                    </span>
                                @endif
                            </td>

                            {{-- KOLOM 5: AKSI --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- DETAIL --}}
                                    <a href="{{ route('purchase_returns.show', $return->id) }}" class="p-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:text-indigo-600 hover:bg-slate-50 transition-colors" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>

                                    {{-- CETAK (Hanya jika Approved) --}}
                                    @if($return->status == 'approved')
                                        <a href="{{ route('purchase_returns.print', $return->id) }}" target="_blank" class="p-2 bg-white border border-slate-200 rounded-lg text-slate-600 hover:text-red-600 hover:bg-red-50 transition-colors" title="Cetak Nota Retur">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 bg-slate-50">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                    </div>
                                    <p class="font-medium text-slate-500">Belum ada data retur pembelian.</p>
                                    <p class="text-sm text-slate-400 mt-1">Buat retur jika ada barang rusak atau pengembalian ke supplier.</p>
                                    <a href="{{ route('purchase_returns.create') }}" class="mt-4 text-red-600 hover:underline font-bold text-sm">Buat Retur Baru &rarr;</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- PAGINATION --}}
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $returns->links() }}
            </div>
        </div>
    </div>
</x-app-layout>