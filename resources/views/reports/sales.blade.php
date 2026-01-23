<x-app-layout>
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Laporan Penjualan (Invoice)</h1>
                <p class="text-slate-500 text-sm">Rekapitulasi pendapatan berdasarkan faktur yang diterbitkan.</p>
            </div>
            
            <form method="GET" action="{{ route('reports.sales') }}" class="flex items-center gap-2 bg-white p-2 rounded-lg border border-slate-200 shadow-sm">
                <input type="date" name="start_date" value="{{ $startDate }}" class="border-slate-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <span class="text-slate-400">-</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="border-slate-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-bold hover:bg-indigo-700 transition-colors">Filter</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-emerald-600 text-white p-6 rounded-xl shadow-lg border border-emerald-500">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-emerald-100 text-xs font-bold uppercase mb-1">Total Pendapatan (Invoiced)</div>
                        <div class="text-3xl font-mono font-bold tracking-tight">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                    </div>
                    <div class="p-2 bg-emerald-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="text-xs text-emerald-200 mt-3 font-medium px-2 py-1 bg-emerald-700/50 rounded inline-block">
                    *Berdasarkan Invoice Terbit
                </div>
            </div>

            <div class="bg-white border border-slate-200 p-6 rounded-xl shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="text-slate-500 text-xs font-bold uppercase mb-1">Volume Transaksi</div>
                        <div class="text-3xl font-mono font-bold text-slate-800">{{ $totalTransactions }} <span class="text-sm font-normal text-slate-400 font-sans">Faktur</span></div>
                    </div>
                    <div class="p-2 bg-slate-100 rounded-lg">
                        <svg class="w-6 h-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">No. Invoice</th>
                            <th class="px-6 py-4">Tanggal Faktur</th>
                            <th class="px-6 py-4">Customer</th>
                            <th class="px-6 py-4 text-center">Status Pembayaran</th>
                            <th class="px-6 py-4 text-right">Nominal Tagihan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-indigo-600 font-mono">{{ $inv->invoice_number }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">Ref SO: {{ $inv->salesOrder->so_number }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                {{ date('d/m/Y', strtotime($inv->date)) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-800">{{ $inv->salesOrder->customer->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($inv->status == 'paid')
                                    <span class="bg-green-100 text-green-800 text-xs px-2.5 py-1 rounded-full font-bold border border-green-200">Lunas</span>
                                @else
                                    <span class="bg-red-100 text-red-800 text-xs px-2.5 py-1 rounded-full font-bold border border-red-200">Belum Bayar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="font-bold text-slate-800">Rp {{ number_format($inv->total_amount, 0, ',', '.') }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">
                                Tidak ada invoice yang diterbitkan pada periode ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>