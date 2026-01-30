<x-app-layout>
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Laporan Hutang Dagang</h1>
                <p class="text-slate-500 text-sm mt-1">Analisa umur hutang supplier (AP Aging) & total kewajiban.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('reports.index') }}" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 font-medium text-sm transition-colors">
                    &larr; Kembali
                </a>
                <a href="{{ route('reports.debt.print') }}" target="_blank" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-bold shadow-sm flex items-center gap-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak PDF
                </a>
            </div>
        </div>

        {{-- KARTU RINGKASAN TOTAL --}}
        <div class="bg-red-50 border border-red-200 rounded-xl p-6 mb-8 flex flex-col md:flex-row justify-between items-center gap-4 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-red-800 font-bold uppercase text-xs tracking-wider">Total Hutang Perusahaan</h3>
                    <p class="text-sm text-red-600">Kewajiban yang harus dibayar ke supplier.</p>
                </div>
            </div>
            <div class="text-3xl md:text-4xl font-mono font-bold text-red-700">
                Rp {{ number_format($grandTotalDebt, 0, ',', '.') }}
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-6 py-4 w-1/4">Nama Supplier</th>
                            <th class="px-6 py-4 text-center">Termin</th>
                            <th class="px-6 py-4 text-right">Total Hutang</th>
                            <th class="px-6 py-4 text-right text-green-600">Belum Jatuh Tempo</th>
                            <th class="px-6 py-4 text-right text-yellow-600">0 - 30 Hari</th>
                            <th class="px-6 py-4 text-right text-orange-600">31 - 60 Hari</th>
                            <th class="px-6 py-4 text-right text-red-600">> 60 Hari</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($suppliers as $supplier)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 text-base">{{ $supplier->name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">{{ $supplier->phone ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold border border-slate-200">
                                    {{ $supplier->term_days }} Hari
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-mono font-bold text-slate-800 text-base bg-slate-50/50">
                                Rp {{ number_format($supplier->total_debt, 0, ',', '.') }}
                            </td>
                            
                            {{-- ANALISA UMUR HUTANG --}}
                            <td class="px-6 py-4 text-right font-mono text-slate-600">
                                @if($supplier->not_due > 0)
                                    <span class="font-bold text-green-600">{{ number_format($supplier->not_due, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-slate-600">
                                @if($supplier->days_0_30 > 0)
                                    <span class="font-bold text-yellow-600">{{ number_format($supplier->days_0_30, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-slate-600">
                                @if($supplier->days_31_60 > 0)
                                    <span class="font-bold text-orange-600">{{ number_format($supplier->days_31_60, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-slate-600 bg-red-50/30">
                                @if($supplier->days_61_plus > 0)
                                    <span class="font-bold text-red-600">{{ number_format($supplier->days_61_plus, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-slate-400 bg-slate-50">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-green-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <h3 class="text-lg font-bold text-slate-600">Tidak Ada Hutang</h3>
                                    <p class="text-slate-500 text-sm mt-1">Luar biasa! Semua tagihan supplier sudah lunas.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($suppliers->count() > 0)
                    <tfoot class="bg-slate-100 border-t border-slate-200 font-bold text-slate-700">
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-right uppercase text-xs tracking-wider">Total Keseluruhan</td>
                            <td class="px-6 py-4 text-right font-mono text-base text-red-700">Rp {{ number_format($grandTotalDebt, 0, ',', '.') }}</td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
        
        <div class="mt-4 text-xs text-slate-400 italic">
            * Kolom "Belum Jatuh Tempo" dihitung jika tanggal hari ini masih sebelum (Tgl PO + Termin).
            <br>
            * Kolom hari (0-30, dst) menunjukkan berapa hari keterlambatan pembayaran dari tanggal jatuh tempo.
        </div>
    </div>
</x-app-layout>