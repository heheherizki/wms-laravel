<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-slate-900">Tambah Akun Baru</h1>
            <p class="text-slate-500 text-sm mt-1">Buat dompet kas tunai atau rekening bank baru.</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 sm:p-8">
            <form action="{{ route('finance.accounts.store') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    {{-- Nama Akun --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Akun / Bank <span class="text-red-500">*</span></label>
                        <input type="text" name="name" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: Bank BCA, Kas Kecil, Kas Besar" required>
                        <p class="text-xs text-slate-400 mt-1">Gunakan nama yang mudah dikenali.</p>
                    </div>

                    {{-- No Rekening --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nomor Rekening (Opsional)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            </div>
                            <input type="text" name="account_number" class="pl-10 w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: 123-456-7890">
                        </div>
                    </div>

                    {{-- Saldo Awal --}}
                    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Saldo Awal (Opening Balance)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-slate-500 font-bold">Rp</span>
                            </div>
                            <input type="number" name="balance" class="pl-10 w-full rounded-lg border-slate-300 focus:ring-green-500 focus:border-green-500 font-mono font-bold text-lg text-slate-800" placeholder="0" min="0">
                        </div>
                        <p class="text-xs text-orange-600 mt-2 flex items-start gap-1">
                            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Pastikan saldo awal sesuai dengan kondisi fisik/bank saat ini.
                        </p>
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Keterangan / Deskripsi</label>
                        <textarea name="description" rows="3" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Keterangan tambahan (mis: Digunakan untuk operasional gudang)"></textarea>
                    </div>
                </div>

                <div class="mt-8 flex items-center justify-end gap-3 border-t pt-6 border-slate-100">
                    <a href="{{ route('finance.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-600 font-bold hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-2.5 rounded-lg font-bold shadow-lg hover:shadow-xl transition-all">
                        Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>