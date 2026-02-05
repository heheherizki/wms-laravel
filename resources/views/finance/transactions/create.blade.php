<x-app-layout>
    <div class="max-w-2xl mx-auto" x-data="{ type: 'out' }">
        
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Catat Transaksi</h1>
                <p class="text-slate-500 text-sm mt-1">Input pengeluaran operasional atau pemasukan lain-lain.</p>
            </div>
            <a href="{{ route('finance.transactions.index') }}" class="text-slate-500 hover:text-slate-700 text-sm font-medium">&larr; Riwayat</a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            
            {{-- TAB SWITCHER TYPE --}}
            <div class="flex border-b border-slate-200">
                <button @click="type = 'out'" 
                    class="flex-1 py-4 text-center font-bold text-sm transition-colors border-b-2"
                    :class="type === 'out' ? 'border-red-500 text-red-600 bg-red-50' : 'border-transparent text-slate-500 hover:bg-slate-50'">
                    📤 PENGELUARAN (Expense)
                </button>
                <button @click="type = 'in'" 
                    class="flex-1 py-4 text-center font-bold text-sm transition-colors border-b-2"
                    :class="type === 'in' ? 'border-green-500 text-green-600 bg-green-50' : 'border-transparent text-slate-500 hover:bg-slate-50'">
                    📥 PEMASUKAN (Income)
                </button>
            </div>

            <form action="{{ route('finance.transactions.store') }}" method="POST" class="p-6 sm:p-8">
                @csrf
                {{-- Hidden input for Type --}}
                <input type="hidden" name="type" x-model="type">

                <div class="space-y-6">
                    
                    {{-- 1. Akun Kas --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Sumber Dana / Akun <span class="text-red-500">*</span></label>
                        <select name="cash_account_id" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="">-- Pilih Dompet --</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">
                                    {{ $acc->name }} (Saldo: Rp {{ number_format($acc->balance, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Kategori (Hanya muncul jika Expense) --}}
                    <div x-show="type === 'out'" x-transition>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Kategori Biaya <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <select name="expense_category_id" class="w-full rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            {{-- Shortcut ke halaman manage kategori --}}
                            <a href="{{ route('finance.index') }}" target="_blank" class="p-2 border rounded-lg text-slate-500 hover:bg-slate-50" title="Kelola Kategori">
                                ⚙️
                            </a>
                        </div>
                    </div>

                    {{-- 3. Jumlah & Tanggal --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nominal (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="amount" class="w-full rounded-lg border-slate-300 font-mono font-bold text-lg" 
                                :class="type === 'out' ? 'text-red-600 focus:ring-red-500' : 'text-green-600 focus:ring-green-500'" 
                                placeholder="0" min="1" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Transaksi</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-slate-300 text-sm" required>
                        </div>
                    </div>

                    {{-- 4. Keterangan --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Keterangan / Deskripsi <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="2" class="w-full rounded-lg border-slate-300" placeholder="Contoh: Bayar Listrik Bulan Januari / Beli Bensin Truk B 1234 XX" required></textarea>
                    </div>

                    {{-- 5. Ref ID --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">No. Referensi / Bukti (Opsional)</label>
                        <input type="text" name="reference_id" class="w-full rounded-lg border-slate-300 text-sm" placeholder="Contoh: INV-PLN-001">
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end">
                    <button type="submit" 
                        class="px-8 py-3 rounded-xl font-bold text-white shadow-lg hover:shadow-xl transition-all w-full sm:w-auto"
                        :class="type === 'out' ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'">
                        <span x-text="type === 'out' ? 'Simpan Pengeluaran' : 'Simpan Pemasukan'"></span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>