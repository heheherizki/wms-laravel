<x-app-layout>
    <div class="max-w-4xl mx-auto">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Detail Retur #{{ $return->id }}</h1>
            <a href="{{ route('returns.index') }}" class="text-slate-500 hover:text-slate-800 text-sm font-bold">&larr; Kembali</a>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mb-6 flex justify-between items-center">
            <div>
                <p class="text-xs text-slate-500 uppercase tracking-wider font-bold">Status Saat Ini</p>
                @if($return->status == 'pending')
                    <span class="text-2xl font-bold text-yellow-600">Menunggu Persetujuan</span>
                    <p class="text-sm text-slate-400 mt-1">Stok belum masuk kembali ke gudang.</p>
                @elseif($return->status == 'approved')
                    <span class="text-2xl font-bold text-green-600">Disetujui (Selesai)</span>
                    <p class="text-sm text-slate-400 mt-1">Stok sudah ditambahkan ke gudang.</p>
                @else
                    <span class="text-2xl font-bold text-red-600">Ditolak</span>
                @endif
            </div>

            @if($return->status == 'pending' && Auth::user()->role == 'admin')
            <div class="flex gap-3">
                <form action="{{ route('returns.reject', $return->id) }}" method="POST" onsubmit="return confirm('Tolak pengajuan retur ini?');">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-4 py-2 border border-red-200 text-red-600 rounded-lg font-bold hover:bg-red-50">Tolak</button>
                </form>
                
                <form action="{{ route('returns.approve', $return->id) }}" method="POST" onsubmit="return confirm('Setujui retur? Stok produk akan bertambah otomatis.');">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 shadow-lg shadow-green-200">
                        Setujui & Restock Barang
                    </button>
                </form>
            </div>
            @endif
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
            <div class="p-6 border-b border-slate-100 grid grid-cols-2 gap-6">
                <div>
                    <span class="text-xs text-slate-500 block">Referensi Sales Order (SO)</span>
                    <span class="font-bold text-indigo-600 text-lg">{{ $return->salesOrder->so_number }}</span>
                    <div class="text-sm text-slate-600 mt-1">{{ $return->salesOrder->customer->name }}</div>
                </div>
                <div>
                    <span class="text-xs text-slate-500 block">Alasan Retur</span>
                    <p class="font-medium text-slate-800 italic">"{{ $return->reason }}"</p>
                    <div class="text-xs text-slate-400 mt-2">Diajukan oleh: {{ $return->user->name }} pada {{ date('d M Y', strtotime($return->date)) }}</div>
                </div>
            </div>
            
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 font-bold">
                    <tr>
                        <th class="px-6 py-3">Nama Produk</th>
                        <th class="px-6 py-3 text-center">Jumlah Dikembalikan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($return->details as $item)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">{{ $item->product->name }}</div>
                            <div class="text-xs text-slate-500">{{ $item->product->sku }}</div>
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-red-600">
                            {{ $item->quantity }} {{ $item->product->unit }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>