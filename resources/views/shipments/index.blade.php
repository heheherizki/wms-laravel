<x-app-layout>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Data Pengiriman (Shipments)</h1>
            <p class="text-slate-500 text-sm">Monitoring surat jalan keluar.</p>
        </div>
        
        <form method="GET" class="flex gap-2">
            <input type="date" name="date" value="{{ request('date') }}" class="border-slate-300 rounded-lg text-sm" onchange="this.form.submit()">
            @if(request('date'))
                <a href="{{ route('shipments.index') }}" class="px-3 py-2 bg-slate-200 rounded-lg text-sm">Reset</a>
            @endif
        </form>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4">No Surat Jalan</th>
                    <th class="px-6 py-4">Ref. SO</th>
                    <th class="px-6 py-4">Tanggal Kirim</th>
                    <th class="px-6 py-4">Customer</th>
                    <th class="px-6 py-4">Info / Supir</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($shipments as $sj)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 font-bold text-blue-600">
                        <a href="{{ route('shipments.print', $sj->id) }}" target="_blank">{{ $sj->shipment_number }}</a>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('sales.show', $sj->sales_order_id) }}" class="text-indigo-600 hover:underline">
                            {{ $sj->salesOrder->so_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4">{{ date('d/m/Y', strtotime($sj->date)) }}</td>
                    <td class="px-6 py-4">{{ $sj->salesOrder->customer->name }}</td>
                    <td class="px-6 py-4 text-slate-500">{{ $sj->notes }}</td>
                    <td class="px-6 py-4 text-center flex justify-center gap-2">
                        <a href="{{ route('shipments.print', $sj->id) }}" target="_blank" class="text-slate-500 hover:text-blue-600 border border-slate-300 px-3 py-1 rounded text-xs font-bold bg-white">
                            SJ
                        </a>

                        @if(!$sj->invoice)
                            <form action="{{ route('invoices.createFromShipment', $sj->id) }}" method="POST" onsubmit="return confirm('Buat Invoice untuk Surat Jalan ini?');">
                                @csrf
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-bold shadow-sm">
                                    + Invoice
                                </button>
                            </form>
                        @else
                            <span class="text-green-600 text-xs font-bold border border-green-200 bg-green-50 px-2 py-1 rounded cursor-default">
                                Terbit
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-slate-400">Belum ada data pengiriman.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $shipments->links() }}</div>
    </div>
</x-app-layout>