<x-app-layout>
    <div class="max-w-5xl mx-auto bg-white border border-slate-200 shadow-lg rounded-xl overflow-hidden print:shadow-none print:border-none">
        
        {{-- HEADER LAPORAN --}}
        <div class="p-8 border-b border-slate-200 bg-slate-50 print:bg-white">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">STATEMENT OF ACCOUNT</h1>
                    <p class="text-slate-500 text-sm mt-1">Laporan Riwayat Transaksi & Saldo Piutang</p>
                </div>
                <div class="text-right">
                    <h2 class="text-xl font-bold text-indigo-600">{{ $customer->name }}</h2>
                    <p class="text-sm text-slate-500">{{ $customer->address }}</p>
                    <p class="text-sm text-slate-500">{{ $customer->phone }}</p>
                </div>
            </div>

            {{-- FILTER TANGGAL --}}
            <div class="mt-6 flex justify-between items-end print:hidden">
                <form action="{{ route('customers.statement', $customer->id) }}" method="GET" class="flex gap-4 items-end">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="block mt-1 text-sm border-slate-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="block mt-1 text-sm border-slate-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-bold shadow-sm">
                        Filter
                    </button>
                </form>
                <button onclick="window.print()" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 px-4 py-2 rounded-md text-sm font-bold shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print PDF
                </button>
            </div>
            
            {{-- Info Periode untuk Print --}}
            <div class="hidden print:block mt-4 text-sm text-slate-600">
                Periode: <strong>{{ date('d M Y', strtotime($startDate)) }}</strong> s/d <strong>{{ date('d M Y', strtotime($endDate)) }}</strong>
            </div>
        </div>

        {{-- TABEL TRANSAKSI --}}
        <div class="p-8">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Keterangan / No. Ref</th>
                        <th class="px-4 py-3 text-right">Tagihan (Debit)</th>
                        <th class="px-4 py-3 text-right">Pembayaran (Kredit)</th>
                        <th class="px-4 py-3 text-right">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    
                    {{-- SALDO AWAL --}}
                    <tr class="bg-slate-50 font-bold text-slate-600">
                        <td class="px-4 py-3" colspan="2">SALDO AWAL (Opening Balance)</td>
                        <td class="px-4 py-3 text-right">-</td>
                        <td class="px-4 py-3 text-right">-</td>
                        <td class="px-4 py-3 text-right">{{ number_format($openingBalance, 0, ',', '.') }}</td>
                    </tr>

                    @php
                        $runningBalance = $openingBalance;
                        $totalDebit = 0;
                        $totalCredit = 0;
                    @endphp

                    @foreach($transactions as $trx)
                        @php
                            $runningBalance += ($trx->debit - $trx->credit);
                            $totalDebit += $trx->debit;
                            $totalCredit += $trx->credit;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-600 whitespace-nowrap">{{ date('d/m/Y', strtotime($trx->date)) }}</td>
                            <td class="px-4 py-3">
                                <span class="font-bold {{ $trx->type == 'INVOICE' ? 'text-indigo-600' : 'text-emerald-600' }} text-xs mr-2 border px-1 rounded">
                                    {{ $trx->type }}
                                </span>
                                {{ $trx->description }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono {{ $trx->debit > 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                {{ $trx->debit > 0 ? number_format($trx->debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono {{ $trx->credit > 0 ? 'text-emerald-600 font-bold' : 'text-slate-300' }}">
                                {{ $trx->credit > 0 ? '(' . number_format($trx->credit, 0, ',', '.') . ')' : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-900">
                                {{ number_format($runningBalance, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t-2 border-slate-200 bg-slate-50">
                    <tr class="font-bold text-slate-800">
                        <td class="px-4 py-4 text-right" colspan="2">TOTAL PERGERAKAN</td>
                        <td class="px-4 py-4 text-right">{{ number_format($totalDebit, 0, ',', '.') }}</td>
                        <td class="px-4 py-4 text-right text-emerald-600">({{ number_format($totalCredit, 0, ',', '.') }})</td>
                        <td class="px-4 py-4 text-right bg-slate-100 text-lg text-indigo-700">
                            {{ number_format($runningBalance, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>

            {{-- FOOTER INFO --}}
            <div class="mt-8 text-xs text-slate-400 italic text-center">
                Dicetak pada: {{ now()->format('d F Y H:i') }} oleh {{ Auth::user()->name }}
            </div>
        </div>
    </div>
</x-app-layout>