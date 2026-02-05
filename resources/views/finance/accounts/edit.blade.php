<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-slate-900">Edit Akun</h1>
            <p class="text-slate-500 text-sm mt-1">Perbarui informasi akun keuangan.</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <form action="{{ route('finance.accounts.update', $account->id) }}" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="space-y-6">
                    {{-- Nama Akun --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Akun / Bank <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $account->name) }}" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>

                    {{-- No Rekening --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nomor Rekening</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            </div>
                            <input type="text" name="account_number" value="{{ old('account_number', $account->account_number) }}" class="pl-10 w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>

                    {{-- Saldo (Read Only) --}}
                    <div class="bg-slate-100 p-4 rounded-lg border border-slate-200 opacity-70">
                        <label class="block text-sm font-bold text-slate-500 mb-2">Saldo Saat Ini (Tidak bisa diedit)</label>
                        <div class="font-mono font-bold text-xl text-slate-700">
                            Rp {{ number_format($account->balance, 0, ',', '.') }}
                        </div>
                        <p class="text-xs text-slate-500 mt-2">
                            * Untuk mengubah saldo, silakan lakukan transaksi Penyesuaian (Adjustment) atau Mutasi Kas.
                        </p>
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Keterangan / Deskripsi</label>
                        <textarea name="description" rows="3" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $account->description) }}</textarea>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3 border-t pt-6 border-slate-100">
                    <a href="{{ route('finance.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-600 font-bold hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-2.5 rounded-lg font-bold shadow-lg hover:shadow-xl transition-all">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>