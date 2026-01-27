<x-app-layout>
    <div class="flex justify-between items-end mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Laporan Umur Piutang</h1>
            <p class="text-sm text-slate-500">Accounts Receivable (AR) Aging Report</p>
        </div>
        <div class="bg-white border border-slate-200 px-4 py-2 rounded-lg shadow-sm text-right">
            <div class="text-xs text-slate-500 uppercase font-bold">Total Piutang Usaha</div>
            <div class="text-2xl font-mono font-bold text-red-600">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4 text-right text-green-700 bg-green-50">Belum Jatuh Tempo</th>
                        <th class="px-6 py-4 text-right text-yellow-700 bg-yellow-50">1 - 30 Hari</th>
                        <th class="px-6 py-4 text-right text-orange-700 bg-orange-50">31 - 60 Hari</th>
                        <th class="px-6 py-4 text-right text-red-700 bg-red-50">> 60 Hari (Macet)</th>
                        <th class="px-6 py-4 text-right font-bold text-slate-800 bg-slate-100">Total Hutang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($report as $row)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-800">
                            {{ $row->name }}
                        </td>
                        <td class="px-6 py-4 text-right bg-green-50/30">
                            @if($row->not_due > 0)
                                Rp {{ number_format($row->not_due, 0, ',', '.') }}
                            @else - @endif
                        </td>
                        <td class="px-6 py-4 text-right bg-yellow-50/30 font-bold text-yellow-700">
                            @if($row->days_0_30 > 0)
                                Rp {{ number_format($row->days_0_30, 0, ',', '.') }}
                            @else - @endif
                        </td>
                        <td class="px-6 py-4 text-right bg-orange-50/30 font-bold text-orange-700">
                            @if($row->days_31_60 > 0)
                                Rp {{ number_format($row->days_31_60, 0, ',', '.') }}
                            @else - @endif
                        </td>
                        <td class="px-6 py-4 text-right bg-red-50/30 font-bold text-red-600">
                            @if($row->days_61_plus > 0)
                                Rp {{ number_format($row->days_61_plus, 0, ',', '.') }}
                            @else - @endif
                        </td>
                        <td class="px-6 py-4 text-right font-mono font-bold bg-slate-50">
                            Rp {{ number_format($row->total_debt, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">
                            Tidak ada piutang yang beredar. Semua tagihan lunas! 🎉
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-200 text-xs text-slate-500">
            * Laporan ini dihitung secara real-time berdasarkan Invoice yang statusnya Partial/Unpaid.
        </div>
    </div>
</x-app-layout>