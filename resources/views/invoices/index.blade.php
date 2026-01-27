<x-app-layout>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Keuangan & Invoice</h1>
            <p class="text-slate-500 text-sm">Daftar tagihan dan status pembayaran customer.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-4 shadow-sm relative">
            <span class="block sm:inline font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4">No Invoice</th>
                    <th class="px-6 py-4">Ref. Shipment</th>
                    <th class="px-6 py-4">Customer</th>
                    <th class="px-6 py-4">Jatuh Tempo</th>
                    <th class="px-6 py-4 text-right">Total Tagihan</th>
                    <th class="px-6 py-4 text-right">Sisa Tagihan</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($invoices as $inv)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <a href="{{ route('invoices.show', $inv->id) }}" class="font-bold text-indigo-600 hover:text-indigo-800 hover:underline font-mono">
                            {{ $inv->invoice_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-500">
                        <div class="font-bold text-slate-700">{{ $inv->shipment->shipment_number }}</div>
                        <div>SO: {{ $inv->salesOrder->so_number }}</div>
                    </td>
                    <td class="px-6 py-4 font-medium text-slate-700">
                        {{ $inv->salesOrder->customer->name }}
                    </td>
                    <td class="px-6 py-4">
                        {{ date('d/m/Y', strtotime($inv->due_date)) }}
                    </td>
                    <td class="px-6 py-4 text-right font-mono font-bold text-slate-700">
                        Rp {{ number_format($inv->total_amount, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-right font-mono font-bold text-red-600">
                        Rp {{ number_format($inv->remaining_balance, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($inv->status == 'paid')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                                LUNAS
                            </span>
                        @elseif($inv->status == 'partial')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800 border border-orange-200">
                                PARTIAL
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                UNPAID
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center items-center gap-3">
                            <a href="{{ route('invoices.show', $inv->id) }}" class="text-indigo-600 hover:text-indigo-800 font-bold text-xs border border-indigo-200 px-2 py-1 rounded hover:bg-indigo-50">
                                Detail / Bayar
                            </a>
                            <a href="{{ route('invoices.print', $inv->id) }}" target="_blank" class="text-slate-500 hover:text-slate-800" title="Cetak Faktur">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-slate-400 italic">
                        Belum ada invoice diterbitkan. Invoice akan muncul otomatis setelah Shipment dibuat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-200">
            {{ $invoices->links() }}
        </div>
    </div>
</x-app-layout>