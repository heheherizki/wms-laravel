<x-app-layout>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Retur Penjualan (RMA)</h1>
        <a href="{{ route('returns.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-indigo-700 shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat Pengajuan Retur
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded relative mb-4 shadow-sm" role="alert">
            <span class="block sm:inline font-medium">{{ session('success') }}</span>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded relative mb-4 shadow-sm" role="alert">
            <span class="block sm:inline font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4">ID / Tgl</th>
                    <th class="px-6 py-4">Referensi SO</th>
                    <th class="px-6 py-4">Customer</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($returns as $retur)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800">RMA #{{ $retur->id }}</div>
                        <div class="text-xs text-slate-500">{{ date('d/m/Y', strtotime($retur->date)) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-mono text-indigo-600 font-bold bg-indigo-50 px-2 py-0.5 rounded">{{ $retur->salesOrder->so_number }}</span>
                    </td>
                    <td class="px-6 py-4 font-medium text-slate-700">
                        {{ $retur->salesOrder->customer->name }}
                    </td>
                    <td class="px-6 py-4">
                        @if($retur->status == 'approved')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Disetujui
                            </span>
                        @elseif($retur->status == 'rejected')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Ditolak
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 animate-pulse">
                                Pending
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center items-center gap-2">
                            <a href="{{ route('returns.show', $retur->id) }}" class="text-slate-500 hover:text-indigo-600 font-bold text-xs border border-slate-200 hover:border-indigo-300 px-3 py-1.5 rounded-lg transition-all bg-white">
                                Detail
                            </a>
                            
                            <a href="{{ route('returns.print', $retur->id) }}" target="_blank" class="text-slate-500 hover:text-blue-600 font-bold text-xs border border-slate-200 hover:border-blue-300 px-3 py-1.5 rounded-lg transition-all bg-white" title="Cetak Form RMA">
                                🖨️
                            </a>

                            @if($retur->status == 'pending')
                            <form action="{{ route('returns.destroy', $retur->id) }}" method="POST" onsubmit="return confirm('Batalkan pengajuan retur ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs border border-slate-200 hover:border-red-300 px-3 py-1.5 rounded-lg transition-all bg-white" title="Hapus Pengajuan">
                                    🗑️
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data retur.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-200">
            {{ $returns->links() }}
        </div>
    </div>
</x-app-layout>