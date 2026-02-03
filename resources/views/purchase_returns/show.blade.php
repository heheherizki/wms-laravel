<x-app-layout>
    <div class="max-w-5xl mx-auto">
        
        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Detail Retur Pembelian</h1>
                <div class="flex items-center gap-2 mt-1">
                    <span class="font-mono text-slate-500 font-bold">RET-{{ str_pad($return->id, 5, '0', STR_PAD_LEFT) }}</span>
                    <span class="text-slate-300">|</span>
                    <span class="text-sm text-slate-500">Diajukan oleh: {{ $return->user->name }}</span>
                </div>
            </div>
            <a href="{{ route('purchase_returns.index') }}" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 font-bold text-sm transition-colors">
                &larr; Kembali
            </a>
        </div>

        {{-- STATUS BANNER --}}
        <div class="mb-8 p-5 rounded-xl border-l-8 shadow-sm flex flex-col md:flex-row justify-between items-center gap-4
            {{ $return->status == 'pending' ? 'bg-yellow-50 border-yellow-400' : 
               ($return->status == 'approved' ? 'bg-green-50 border-green-500' : 'bg-red-50 border-red-500') }}">
            
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-full {{ $return->status == 'pending' ? 'bg-yellow-100 text-yellow-600' : ($return->status == 'approved' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600') }}">
                    @if($return->status == 'pending')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    @elseif($return->status == 'approved')
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    @else
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    @endif
                </div>
                <div>
                    <h2 class="text-lg font-bold uppercase tracking-wide
                        {{ $return->status == 'pending' ? 'text-yellow-800' : ($return->status == 'approved' ? 'text-green-800' : 'text-red-800') }}">
                        Status: {{ $return->status }}
                    </h2>
                    <p class="text-sm opacity-80">
                        @if($return->status == 'pending')
                            Menunggu persetujuan Manager. Stok belum dipotong.
                        @elseif($return->status == 'approved')
                            Disetujui. Stok gudang telah dikurangi otomatis.
                        @else
                            Pengajuan ditolak. Tidak ada perubahan stok.
                        @endif
                    </p>
                </div>
            </div>

            {{-- TOMBOL PRINT (Hanya muncul jika Approved) --}}
            @if($return->status == 'approved')
                <a href="{{ route('purchase_returns.print', $return->id) }}" target="_blank" class="bg-white border border-green-200 text-green-700 px-5 py-2.5 rounded-lg text-sm font-bold shadow-sm hover:bg-green-50 hover:text-green-800 transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Nota Retur
                </a>
            @endif
        </div>

        {{-- GRID INFO --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            {{-- KIRI: INFO SUPPLIER --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 font-bold text-slate-700">
                    Informasi Supplier & PO
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <label class="text-xs text-slate-400 uppercase font-bold">Asal Purchase Order</label>
                        <div class="text-lg font-bold text-indigo-600 flex items-center gap-2">
                            {{ $return->purchase->po_number }}
                            <a href="{{ route('purchases.show', $return->purchase->id) }}" class="text-xs bg-indigo-50 px-2 py-1 rounded border border-indigo-100 hover:bg-indigo-100" title="Lihat PO">↗</a>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="text-xs text-slate-400 uppercase font-bold">Nama Supplier</label>
                        <div class="text-base font-bold text-slate-800">{{ $return->purchase->supplier->name }}</div>
                        <div class="text-sm text-slate-500">{{ $return->purchase->supplier->phone ?? '-' }}</div>
                    </div>
                    <div>
                        <label class="text-xs text-slate-400 uppercase font-bold">Tanggal PO</label>
                        <div class="text-sm text-slate-600">{{ date('d F Y', strtotime($return->purchase->date)) }}</div>
                    </div>
                </div>
            </div>

            {{-- KANAN: INFO RETUR --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 font-bold text-slate-700">
                    Detail Pengembalian
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <label class="text-xs text-slate-400 uppercase font-bold">Tanggal Pengajuan</label>
                        <div class="text-base font-bold text-slate-800">{{ date('d F Y', strtotime($return->date)) }}</div>
                    </div>
                    <div>
                        <label class="text-xs text-slate-400 uppercase font-bold">Alasan Retur</label>
                        <div class="mt-1 p-3 bg-red-50 border border-red-100 rounded-lg text-red-800 italic text-sm">
                            "{{ $return->reason }}"
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL BARANG --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 font-bold text-slate-700">
                Barang yang Diretur
            </div>
            <table class="w-full text-sm text-left">
                <thead class="bg-white text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">Produk</th>
                        <th class="px-6 py-3 text-center">Satuan</th>
                        <th class="px-6 py-3 text-center w-40">Qty Retur</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($return->details as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800 text-base">{{ $item->product->name }}</div>
                            <div class="text-xs text-slate-500">{{ $item->product->sku }}</div>
                        </td>
                        <td class="px-6 py-4 text-center text-slate-500">
                            {{ $item->product->unit }}
                        </td>
                        <td class="px-6 py-4 text-center bg-red-50/30">
                            <span class="text-xl font-bold text-red-600">{{ $item->quantity }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ACTION BUTTONS (APPROVE/REJECT) --}}
        @if($return->status == 'pending')
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-lg flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-sm text-slate-500">
                    <strong class="text-slate-800 block mb-1">Konfirmasi Persetujuan</strong>
                    Pastikan barang fisik sudah siap dikirim kembali ke Supplier sebelum menyetujui.
                </div>
                
                <div class="flex gap-3">
                    {{-- TOLAK --}}
                    <form action="{{ route('purchase_returns.reject', $return->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK pengajuan retur ini?');">
                        @csrf @method('PATCH')
                        <button type="submit" class="px-5 py-2.5 border border-slate-300 rounded-xl text-slate-600 font-bold hover:bg-slate-100 transition-colors">
                            Tolak Pengajuan
                        </button>
                    </form>
                    
                    {{-- SETUJUI --}}
                    <form action="{{ route('purchase_returns.approve', $return->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Stok barang di gudang akan BERKURANG otomatis. Lanjutkan?');">
                        @csrf @method('PATCH')
                        <button type="submit" class="px-6 py-2.5 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Setujui & Potong Stok
                        </button>
                    </form>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>