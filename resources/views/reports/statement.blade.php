<x-app-layout>
    <div class="max-w-7xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-slate-900">Statement of Account (SOA)</h1>
            <p class="text-slate-500 text-sm">Kartu Riwayat Hutang Supplier (Rekening Koran).</p>
        </div>

        {{-- FILTER SECTION --}}
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mb-6">
            <form method="GET" action="{{ route('reports.statement') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Supplier</label>
                    <select name="supplier_id" class="w-full rounded-lg border-slate-300 text-sm" required>
                        <option value="">-- Pilih --</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-t')) }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-bold text-sm w-full transition-colors">
                        Tampilkan
                    </button>
                    @if(request('supplier_id'))
                        <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg font-bold text-sm flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        @if($selectedSupplier)
            {{-- SUMMARY CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl">
                    <div class="text-xs text-slate-500 uppercase font-bold">Saldo Awal ({{ date('d M Y', strtotime(request('start_date'))) }})</div>
                    <div class="text-xl font-mono font-bold text-slate-700">Rp {{ number_format($openingBalance, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white border border-slate-200 p-4 rounded-xl">
                    <div class="text-xs text-slate-500 uppercase font-bold">Mutasi (Debet - Kredit)</div>
                    <div class="text-xl font-mono font-bold {{ ($endingBalance - $openingBalance) > 0 ? 'text-red-600' : 'text-green-600' }}">
                        Rp {{ number_format($endingBalance - $openingBalance, 0, ',', '.') }}
                    </div>
                </div>
                <div class="bg-indigo-50 border border-indigo-200 p-4 rounded-xl">
                    <div class="text-xs text-indigo-500 uppercase font-bold">Saldo Akhir</div>
                    <div class="text-2xl font-mono font-bold text-indigo-700">Rp {{ number_format($endingBalance, 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- STATEMENT TABLE --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Ref #</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3 text-right">Tagihan (Masuk)</th>
                            <th class="px-4 py-3 text-right">Bayar/Retur (Keluar)</th>
                            <th class="px-4 py-3 text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {{-- SALDO AWAL --}}
                        <tr class="bg-slate-50 italic">
                            <td colspan="5" class="px-4 py-3 text-right font-bold text-slate-500">Saldo Awal</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-700">
                                {{ number_format($openingBalance, 0, ',', '.') }}
                            </td>
                        </tr>

                        @php $runningBalance = $openingBalance; @endphp

                        @forelse($statement as $row)
                            @php 
                                $runningBalance += ($row['credit'] - $row['debit']); 
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">{{ date('d/m/Y', strtotime($row['date'])) }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-indigo-600">
                                    {{ $row['ref'] }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-xs block text-slate-700">{{ $row['type'] }}</span>
                                    <span class="text-xs text-slate-500">{{ Str::limit($row['description'], 50) }}</span>
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-red-600">
                                    {{ $row['credit'] > 0 ? number_format($row['credit'], 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-green-600">
                                    {{ $row['debit'] > 0 ? number_format($row['debit'], 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-slate-800 bg-slate-50">
                                    {{ number_format($runningBalance, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-400">Tidak ada transaksi pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 bg-slate-50 border border-slate-200 rounded-xl border-dashed">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <p class="text-slate-500">Silakan pilih Supplier dan Tanggal untuk melihat Statement.</p>
            </div>
        @endif
    </div>
</x-app-layout>