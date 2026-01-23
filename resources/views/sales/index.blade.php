<x-app-layout>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Sales Order (Penjualan)</h1>
            <p class="text-slate-500 text-sm">Kelola pesanan dari customer.</p>
        </div>
        <a href="{{ route('sales.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-sm flex items-center gap-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Buat SO Baru
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
                    <th class="px-6 py-4">No SO</th>
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4">Customer</th>
                    <th class="px-6 py-4 text-right">Total Nilai</th>
                    <th class="px-6 py-4 text-center">Status Barang</th>
                    <th class="px-6 py-4 text-center">Pembayaran</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($orders as $so)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 font-bold text-blue-600">
                        <a href="{{ route('sales.show', $so->id) }}">{{ $so->so_number }}</a>
                    </td>
                    <td class="px-6 py-4">{{ date('d/m/Y', strtotime($so->date)) }}</td>
                    <td class="px-6 py-4 font-medium text-slate-800">{{ $so->customer->name }}</td>
                    <td class="px-6 py-4 text-right font-mono font-bold text-slate-700">
                        Rp {{ number_format($so->grand_total, 0, ',', '.') }}
                    </td>
                    
                    <td class="px-6 py-4 text-center">
                        @if($so->status == 'pending')
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2.5 py-1 rounded-full font-bold">Pending</span>
                        @elseif($so->status == 'partial')
                            <span class="bg-orange-100 text-orange-800 text-xs px-2.5 py-1 rounded-full font-bold">Partial</span>
                        @elseif($so->status == 'shipped')
                            <span class="bg-blue-100 text-blue-800 text-xs px-2.5 py-1 rounded-full font-bold">Selesai</span>
                        @else
                            <span class="bg-red-100 text-red-800 text-xs px-2.5 py-1 rounded-full font-bold">Batal</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-center">
                        @if($so->payment_status == 'paid')
                            <span class="text-green-600 font-bold text-xs border border-green-200 bg-green-50 px-2 py-1 rounded">Lunas</span>
                        @else
                            <span class="text-slate-500 font-bold text-xs border border-slate-200 bg-slate-50 px-2 py-1 rounded">Belum Lunas</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('sales.show', $so->id) }}" class="text-slate-500 hover:text-blue-600 font-bold text-xs border border-slate-300 hover:border-blue-600 px-3 py-1 rounded-md transition-all">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                        Belum ada transaksi penjualan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>