<x-app-layout>
    <div x-data="{ 
        openCreate: false, 
        openEdit: false, 
        selectedItem: null,
        selectItem(item) { this.selectedItem = item; }
    }">
        <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Data Customer</h1>
                <p class="text-slate-500 mt-1">Kelola data pelanggan dan term pembayaran.</p>
            </div>
            <button @click="openCreate = true" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-sm flex items-center gap-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Customer
            </button>
        </div>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="mb-4 bg-green-500 text-white px-4 py-3 rounded-lg shadow-sm text-sm font-bold">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Kode</th>
                            <th class="px-6 py-4">Nama Customer</th>
                            <th class="px-6 py-4">Term Pembayaran</th>
                            <th class="px-6 py-4">Kontak</th>
                            <th class="px-6 py-4">Alamat</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($customers as $cust)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs font-bold text-slate-500">{{ $cust->code }}</td>
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $cust->name }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $cust->payment_terms }} Hari
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div class="font-medium text-slate-700">{{ $cust->phone }}</div>
                                <div class="text-slate-400">{{ $cust->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500 truncate max-w-xs">{{ $cust->address }}</td>
                            <td class="px-6 py-4 text-center">
                                <button @click="openEdit = true; selectItem({{ $cust }})" class="text-slate-400 hover:text-blue-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada data customer.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-75" @click="openCreate = false"></div>
                <form action="{{ route('customers.store') }}" method="POST" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg font-bold text-slate-900 mb-4">Tambah Customer Baru</h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-3 gap-3">
                                <div class="col-span-1">
                                    <label class="block text-xs font-bold text-slate-700 uppercase">Kode</label>
                                    <input type="text" name="code" required class="w-full rounded-lg border-slate-300 text-sm" placeholder="CUST-001">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-slate-700 uppercase">Nama Customer</label>
                                    <input type="text" name="name" required class="w-full rounded-lg border-slate-300 text-sm" placeholder="Nama Perusahaan/Orang">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Term Pembayaran (Hari)</label>
                                <input type="number" name="payment_terms" value="0" required class="w-full rounded-lg border-slate-300 text-sm" placeholder="0 untuk Cash">
                                <p class="text-[10px] text-slate-400 mt-1">Contoh: Isi 30 untuk Net 30 Days.</p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">No. Telepon</label>
                                    <input type="text" name="phone" class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">Email</label>
                                    <input type="email" name="email" class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Alamat Lengkap</label>
                                <textarea name="address" rows="2" class="w-full rounded-lg border-slate-300 text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 flex justify-end gap-2">
                        <button @click="openCreate = false" type="button" class="px-4 py-2 border rounded-lg bg-white text-slate-700 hover:bg-slate-50 text-sm font-medium">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-75" @click="openEdit = false"></div>
                
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    
                    <form id="editCustForm" x-bind:action="'/customers/' + (selectedItem ? selectedItem.id : '')" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                            <h3 class="text-lg font-bold text-slate-900 mb-2">Edit Customer</h3>
                            <div class="space-y-4 mt-4">
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="col-span-1">
                                        <label class="block text-xs font-bold text-slate-700 uppercase">Kode</label>
                                        <input type="text" name="code" x-bind:value="selectedItem ? selectedItem.code : ''" required class="w-full rounded-lg border-slate-300 text-sm">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-xs font-bold text-slate-700 uppercase">Nama Customer</label>
                                        <input type="text" name="name" x-bind:value="selectedItem ? selectedItem.name : ''" required class="w-full rounded-lg border-slate-300 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">Term Pembayaran (Hari)</label>
                                    <input type="number" name="payment_terms" x-bind:value="selectedItem ? selectedItem.payment_terms : ''" required class="w-full rounded-lg border-slate-300 text-sm">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase">No. Telepon</label>
                                        <input type="text" name="phone" x-bind:value="selectedItem ? selectedItem.phone : ''" class="w-full rounded-lg border-slate-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase">Email</label>
                                        <input type="email" name="email" x-bind:value="selectedItem ? selectedItem.email : ''" class="w-full rounded-lg border-slate-300 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">Alamat Lengkap</label>
                                    <textarea name="address" rows="2" class="w-full rounded-lg border-slate-300 text-sm" x-text="selectedItem ? selectedItem.address : ''"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="bg-slate-50 px-4 py-3 sm:px-6 flex flex-col-reverse sm:flex-row sm:justify-between items-center gap-3">
                        <form x-bind:action="'/customers/' + (selectedItem ? selectedItem.id : '')" method="POST" onsubmit="return confirm('Hapus customer ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium underline px-2">Hapus Data</button>
                        </form>
                        <div class="flex gap-2 justify-end w-full sm:w-auto">
                            <button @click="openEdit = false" type="button" class="px-4 py-2 border rounded-lg bg-white text-slate-700 hover:bg-slate-50 text-sm font-medium">Batal</button>
                            <button type="submit" form="editCustForm" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>