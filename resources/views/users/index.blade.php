<x-app-layout>
    <div x-data="{ 
        openCreate: false, 
        openEdit: false, 
        selectedUser: null,
        selectUser(user) { this.selectedUser = user; }
    }">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Manajemen User</h1>
                <p class="text-slate-500 mt-1">Kelola akses staff dan administrator.</p>
            </div>
            <button @click="openCreate = true" class="bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Tambah User
            </button>
        </div>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="mb-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-sm text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" class="mb-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-sm text-sm">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div x-data="{ show: true }" x-show="show" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                <strong class="font-bold">Ada kesalahan input!</strong>
                <ul class="mt-1 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="show = false">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-4">Nama</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-bold text-slate-800 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            {{ $user->name }}
                        </td>
                        <td class="px-6 py-4 text-slate-500">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role == 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button @click="openEdit = true; selectUser({{ $user }})" class="text-slate-400 hover:text-orange-600 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-75" @click="openCreate = false"></div>
                <form action="{{ route('users.store') }}" method="POST" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg font-bold text-slate-900 mb-4">Tambah User Baru</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Nama Lengkap</label>
                                <input type="text" name="name" required class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Email</label>
                                <input type="email" name="email" required class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Role</label>
                                <select name="role" class="w-full rounded-lg border-slate-300 text-sm">
                                    <option value="staff">Staff Gudang</option>
                                    <option value="admin">Administrator</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Password</label>
                                <input type="password" name="password" required class="w-full rounded-lg border-slate-300 text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg bg-slate-800 text-white px-4 py-2 text-sm font-medium hover:bg-slate-900 sm:ml-3 sm:w-auto">Simpan</button>
                        <button @click="openCreate = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 sm:mt-0 sm:ml-3 sm:w-auto">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-75" @click="openEdit = false"></div>
                
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    
                    <form id="updateUserForm" x-bind:action="'/users/' + (selectedUser ? selectedUser.id : '')" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                            <h3 class="text-lg font-bold text-slate-900 mb-2">Edit User</h3>
                            <p class="text-sm text-slate-500 mb-4">Edit data untuk <span class="font-bold" x-text="selectedUser ? selectedUser.name : ''"></span></p>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">Nama Lengkap</label>
                                    <input type="text" name="name" x-bind:value="selectedUser ? selectedUser.name : ''" required class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">Email</label>
                                    <input type="email" name="email" x-bind:value="selectedUser ? selectedUser.email : ''" required class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">Role</label>
                                    <select name="role" x-bind:value="selectedUser ? selectedUser.role : ''" class="w-full rounded-lg border-slate-300 text-sm">
                                        <option value="staff">Staff Gudang</option>
                                        <option value="admin">Administrator</option>
                                    </select>
                                </div>
                                <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                    <label class="block text-xs font-bold text-yellow-700 uppercase">Ganti Password (Opsional)</label>
                                    <p class="text-[10px] text-yellow-600 mb-1">Isi hanya jika ingin mereset password user ini.</p>
                                    <input type="password" name="password" class="w-full rounded-lg border-slate-300 text-sm" placeholder="Password Baru...">
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="bg-slate-50 px-4 py-3 sm:px-6 flex flex-col-reverse sm:flex-row sm:justify-between items-center gap-3">
                        
                        <form x-bind:action="'/users/' + (selectedUser ? selectedUser.id : '')" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium underline px-2 py-2">
                                Hapus User
                            </button>
                        </form>

                        <div class="flex gap-2 w-full sm:w-auto justify-end">
                            <button @click="openEdit = false" type="button" class="inline-flex justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</button>
                            <button type="submit" form="updateUserForm" class="inline-flex justify-center rounded-lg bg-orange-600 text-white px-4 py-2 text-sm font-medium hover:bg-orange-700">Simpan Perubahan</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</x-app-layout>