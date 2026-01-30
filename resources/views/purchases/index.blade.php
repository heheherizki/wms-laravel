<x-app-layout>
    <div class="max-w-7xl mx-auto">
        
        {{-- HEADER & TOMBOL BUAT --}}
        <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Purchase Order (PO)</h1>
                <p class="text-slate-500 mt-1">Monitor pembelian barang, penerimaan stok, dan status hutang.</p>
            </div>
            <a href="{{ route('purchases.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-sm flex items-center gap-2 transition-all transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat PO Baru
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded shadow-sm relative flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded shadow-sm relative flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 uppercase tracking-wider text-xs">
                        <tr>
                            <th class="px-6 py-4">No PO / Tanggal</th>
                            <th class="px-6 py-4">Supplier</th>
                            <th class="px-6 py-4 text-center">Status Barang</th>
                            <th class="px-6 py-4 text-center">Pembayaran (Hutang)</th>
                            <th class="px-6 py-4 text-right">Total Nilai</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($purchases as $po)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            
                            {{-- KOLOM 1: NO PO & TANGGAL --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-indigo-600 font-bold font-mono text-base hover:underline">
                                        <a href="{{ route('purchases.show', $po->id) }}">{{ $po->po_number }}</a>
                                    </span>
                                    <span class="text-xs text-slate-400 flex items-center gap-1 mt-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ date('d M Y', strtotime($po->date)) }}
                                    </span>
                                </div>
                            </td>

                            {{-- KOLOM 2: SUPPLIER --}}
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-700">{{ $po->supplier->name }}</div>
                                <div class="text-xs text-slate-400">{{ $po->supplier->phone ?? '-' }}</div>
                            </td>

                            {{-- KOLOM 3: STATUS BARANG (LOGISTIK) --}}
                            <td class="px-6 py-4 text-center">
                                {{-- REVISI: Cek status 'completed' (bukan received) --}}
                                @if($po->status == 'completed')
                                    <div class="inline-flex flex-col items-center justify-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 border border-blue-200 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            DITERIMA
                                        </span>
                                        <span class="text-[10px] text-slate-400 mt-1">Stok Masuk</span>
                                    </div>
                                @elseif($po->status == 'pending')
                                    <div class="inline-flex flex-col items-center justify-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 border border-yellow-200 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            PENDING
                                        </span>
                                        <span class="text-[10px] text-slate-400 mt-1">Menunggu Barang</span>
                                    </div>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                        BATAL
                                    </span>
                                @endif
                            </td>

                            {{-- KOLOM 4: STATUS PEMBAYARAN (KEUANGAN) --}}
                            <td class="px-6 py-4 text-center">
                                @if($po->status == 'canceled')
                                    <span class="text-slate-400 text-xs">-</span>
                                @elseif($po->payment_status == 'paid')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200 flex items-center justify-center gap-1 w-fit mx-auto">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        LUNAS
                                    </span>
                                @elseif($po->payment_status == 'partial')
                                    <div class="flex flex-col items-center">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700 border border-orange-200 w-fit">
                                            CICILAN
                                        </span>
                                        <span class="text-[10px] text-slate-500 mt-1">
                                            Sisa: Rp {{ number_format($po->grand_total - $po->amount_paid, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-100 w-fit mx-auto block">
                                        BELUM BAYAR
                                    </span>
                                @endif
                            </td>

                            {{-- KOLOM 5: TOTAL NILAI --}}
                            <td class="px-6 py-4 text-right">
                                <div class="font-mono font-bold text-slate-700 text-sm">
                                    {{-- PERBAIKAN: Gunakan total_amount --}}
                                    Rp {{ number_format($po->total_amount, 0, ',', '.') }}
                                </div>
                            </td>

                            {{-- KOLOM 6: AKSI --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    
                                    {{-- 1. TOMBOL DETAIL (Selalu Muncul) --}}
                                    <a href="{{ route('purchases.show', $po->id) }}" class="p-2 bg-white border border-slate-200 text-slate-500 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 transition-colors" title="Lihat Detail & Terima Barang">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>

                                    {{-- 2. TOMBOL BAYAR (Jika Belum Lunas & Tidak Batal) --}}
                                    @if($po->payment_status != 'paid' && $po->status != 'canceled')
                                        <a href="{{ route('purchases.pay', $po->id) }}" class="p-2 bg-green-50 border border-green-200 text-green-600 rounded-lg hover:bg-green-100 transition-colors" title="Bayar Hutang">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        </a>
                                    @endif

                                    {{-- 3. TOMBOL PRINT (Shortcut) --}}
                                    <a href="{{ route('purchases.print', $po->id) }}" target="_blank" class="p-2 bg-white border border-slate-200 text-slate-500 rounded-lg hover:bg-slate-100 hover:text-slate-800 transition-colors" title="Cetak PDF">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>

                                    {{-- 4. DROPDOWN MENU (Edit & Hapus - Hanya jika Pending) --}}
                                    @if($po->status == 'pending')
                                        <div x-data="{ open: false }" class="relative">
                                            <button @click="open = !open" @click.away="open = false" class="p-2 bg-white border border-slate-200 text-slate-400 rounded-lg hover:bg-slate-50 hover:text-slate-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                                            </button>
                                            <div x-show="open" class="absolute right-0 mt-2 w-32 bg-white rounded-md shadow-lg border border-slate-100 z-10 py-1" style="display: none;">
                                                <a href="{{ route('purchases.edit', $po->id) }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600">
                                                    Edit Data
                                                </a>
                                                <form action="{{ route('purchases.destroy', $po->id) }}" method="POST" onsubmit="return confirm('Yakin hapus PO ini?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 bg-slate-50">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <p class="font-medium">Belum ada data PO.</p>
                                    <a href="{{ route('purchases.create') }}" class="mt-4 text-indigo-600 hover:underline font-bold text-sm">Buat PO Baru &rarr;</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>