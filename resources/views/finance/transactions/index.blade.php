<x-app-layout>
    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Riwayat Transaksi</h1>
                <p class="text-slate-500 mt-1">Jurnal pengeluaran dan pemasukan kas operasional.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('finance.index') }}" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-600 font-bold text-sm hover:bg-slate-50">
                    Dashboard
                </a>
                <a href="{{ route('finance.transactions.create') }}" class="px-5 py-2 bg-indigo-600 text-white rounded-lg font-bold text-sm hover:bg-indigo-700 shadow-md flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Catat Transaksi
                </a>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm mb-6">
            <form method="GET" action="{{ route('finance.transactions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase">Akun Kas/Bank</label>
                    <select name="account_id" class="w-full text-sm rounded-lg border-slate-300 mt-1">
                        <option value="">Semua Akun</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                                {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full text-sm rounded-lg border-slate-300 mt-1">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full text-sm rounded-lg border-slate-300 mt-1">
                </div>
                <button type="submit" class="bg-slate-800 text-white px-4 py-2.5 rounded-lg text-sm font-bold hover:bg-slate-900">
                    Filter Data
                </button>
            </form>
        </div>

        {{-- TABLE --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4">Akun & Kategori</th>
                        <th class="px-6 py-4">Keterangan</th>
                        <th class="px-6 py-4 text-right">Nominal</th>
                        <th class="px-6 py-4 text-center">User</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-slate-700">{{ date('d/m/Y', strtotime($trx->date)) }}</span>
                            <div class="text-xs text-slate-400 mt-0.5">{{ $trx->created_at->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($trx->type == 'out')
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold border border-red-200">KELUAR</span>
                            @elseif($trx->type == 'in')
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold border border-green-200">MASUK</span>
                            @else
                                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold border border-blue-200">TRANSFER</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">{{ $trx->account->name }}</div>
                            <div class="text-xs text-slate-500">
                                {{ $trx->category->name ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 max-w-xs">
                            <div class="text-slate-700">{{ $trx->description }}</div>
                            @if($trx->reference_id)
                                <div class="text-xs text-slate-400 mt-1 font-mono bg-slate-100 px-1 rounded w-fit">Ref: {{ $trx->reference_id }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-mono font-bold text-base {{ $trx->type == 'out' ? 'text-red-600' : 'text-green-600' }}">
                            {{ $trx->type == 'out' ? '-' : '+' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center text-xs text-slate-500">
                            {{ $trx->user->name }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">Belum ada transaksi tercatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>