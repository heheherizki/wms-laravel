<x-app-layout>
    <div class="max-w-4xl mx-auto">
        
        <div class="mb-6 flex items-center gap-4">
            <a href="{{ route('suppliers.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Edit Supplier</h1>
                <p class="text-slate-500 text-sm">Perbarui data pemasok: {{ $supplier->name }}</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded shadow-sm relative text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
                <div class="p-6 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-800">Informasi Umum</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Kode Supplier</label>
                        <input type="text" name="code" value="{{ old('code', $supplier->code) }}" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama PT / Toko <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $supplier->name) }}" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" required>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Sales Contact Person</label>
                        <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">No. Telepon / HP</label>
                        <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Lengkap</label>
                        <textarea name="address" rows="3" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $supplier->address) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- BAGIAN KEUANGAN --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-6">
                <div class="p-6 border-b border-slate-100 bg-indigo-50">
                    <h3 class="font-bold text-indigo-800 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Data Keuangan (Purchase)
                    </h3>
                </div>
                <div class="p-6">
                    <div class="max-w-md">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Termin Pembayaran (Jatuh Tempo)</label>
                        <div class="relative">
                            <input type="number" name="term_days" value="{{ old('term_days', $supplier->term_days) }}" min="0" class="w-full rounded-lg border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 pr-12 font-bold text-slate-700">
                            <span class="absolute right-4 top-2.5 text-slate-500 font-bold text-sm">Hari</span>
                        </div>
                        <p class="text-sm text-slate-500 mt-2 bg-slate-50 p-2 rounded border border-slate-100">
                            <span class="font-bold">Info:</span> Isi <strong>0</strong> jika pembayaran dilakukan secara Tunai (Cash). Isi <strong>30</strong> jika TOP 30 Hari.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('suppliers.index') }}" class="px-6 py-3 bg-white border border-slate-300 text-slate-700 font-bold rounded-lg hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 shadow-md transition-colors">Simpan Perubahan</button>
            </div>

        </form>
    </div>
</x-app-layout>