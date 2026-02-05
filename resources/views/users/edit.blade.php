<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Edit User</h1>
            <p class="text-slate-500 text-sm mt-1">Update data user: <span class="font-bold">{{ $user->name }}</span></p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Email Login</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Role (Jabatan)</label>
                    <select name="role" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 bg-slate-50">
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ $userRole == $role ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $role)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                    <label class="block text-xs font-bold text-yellow-800 uppercase mb-2">Ubah Password (Opsional)</label>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="password" name="password" placeholder="Password Baru" class="w-full rounded-lg border-slate-300 text-sm">
                        <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" class="w-full rounded-lg border-slate-300 text-sm">
                    </div>
                    <p class="text-xs text-yellow-600 mt-2">* Biarkan kosong jika tidak ingin mengganti password.</p>
                </div>

                <div class="pt-6 flex justify-end gap-3 border-t border-slate-100 mt-6">
                    <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-700 font-bold text-sm hover:bg-slate-50">Batal</a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>