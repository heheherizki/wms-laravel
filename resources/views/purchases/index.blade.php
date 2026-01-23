<x-app-layout>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Purchase Order (PO)</h1>
            <p class="text-slate-500 text-sm">Kelola pemesanan barang ke supplier.</p>
        </div>
        <a href="{{ route('purchases.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat PO Baru
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-500 text-white px-4 py-3 rounded-lg text-sm font-bold shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4">No PO</th>
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4">Supplier</th>
                    <th class="px-6 py-4 text-right">Total</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($purchases as $po)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 font-bold text-indigo-600">
                        <a href="{{ route('purchases.show', $po->id) }}">{{ $po->po_number }}</a>
                    </td>
                    <td class="px-6 py-4">{{ date('d/m/Y', strtotime($po->date)) }}</td>
                    <td class="px-6 py-4">{{ $po->supplier->name }}</td>
                    <td class="px-6 py-4 text-right font-mono font-bold">
                        Rp {{ number_format($po->total_amount, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($po->status == 'pending')
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-bold">Pending</span>
                        @elseif($po->status == 'completed')
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-bold">Selesai</span>
                        @else
                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-bold">Batal</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('purchases.show', $po->id) }}" class="text-slate-500 hover:text-indigo-600 border border-slate-300 hover:border-indigo-600 px-3 py-1 rounded-md text-xs font-bold transition-colors">
                                Detail
                            </a>

                            @if($po->status == 'pending')
                            <a href="{{ route('purchases.edit', $po->id) }}" class="text-slate-500 hover:text-orange-600 border border-slate-300 hover:border-orange-600 px-3 py-1 rounded-md text-xs font-bold transition-colors">
                                Edit
                            </a>
                            @endif
                            
                            @if($po->status == 'pending')
                            <form action="{{ route('purchases.destroy', $po->id) }}" method="POST" onsubmit="return confirm('Hapus PO ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-600 p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada transaksi pembelian.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>