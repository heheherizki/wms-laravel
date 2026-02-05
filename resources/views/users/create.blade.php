<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Tambah User Baru</h1>
            <p class="text-slate-500 text-sm mt-1">Buat akun login untuk staff baru.</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <form action="{{ route('users.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Email Login</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Role (Jabatan)</label>
                    <select name="role" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 bg-slate-50">
                        @foreach($roles as $role)
                            <option value="{{ $role }}">{{ ucwords(str_replace('_', ' ', $role)) }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-2">Pilih role sesuai tanggung jawab user.</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                        <input type="password" name="password" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500" required>
                    </div>
                </div>

                <div class="pt-6 flex justify-end gap-3 border-t border-slate-100 mt-6">
                    <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-700 font-bold text-sm hover:bg-slate-50">Batal</a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-md">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>