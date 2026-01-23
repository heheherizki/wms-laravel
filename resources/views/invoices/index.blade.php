<x-app-layout>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Keuangan & Invoice</h1>
            <p class="text-slate-500 text-sm">Daftar tagihan berdasarkan surat jalan.</p>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4">No Invoice</th>
                    <th class="px-6 py-4">Ref. Shipment</th>
                    <th class="px-6 py-4">Customer</th>
                    <th class="px-6 py-4">Jatuh Tempo</th>
                    <th class="px-6 py-4 text-right">Total Tagihan</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($invoices as $inv)
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-4 font-bold text-blue-600">
                        {{ $inv->invoice_number }}
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-500">
                        {{ $inv->shipment->shipment_number }}<br>
                        (SO: {{ $inv->salesOrder->so_number }})
                    </td>
                    <td class="px-6 py-4 font-medium">{{ $inv->salesOrder->customer->name }}</td>
                    <td class="px-6 py-4 text-red-600 font-medium">{{ date('d/m/Y', strtotime($inv->due_date)) }}</td>
                    <td class="px-6 py-4 text-right font-mono font-bold text-slate-700">
                        Rp {{ number_format($inv->total_amount, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($inv->status == 'paid')
                            <span class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-bold">Lunas</span>
                        @else
                            <span class="bg-red-100 text-red-800 text-xs px-2.5 py-1 rounded-full font-bold">Belum Bayar</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('invoices.print', $inv->id) }}" target="_blank" class="text-blue-600 hover:underline font-bold text-xs">Cetak</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-slate-400">Belum ada invoice diterbitkan.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $invoices->links() }}</div>
    </div>
</x-app-layout>