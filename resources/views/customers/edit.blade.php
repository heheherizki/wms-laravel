<x-app-layout>
    <div class="max-w-4xl mx-auto">
        
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Edit Data Customer</h1>
                <p class="text-sm text-slate-500">Perbarui informasi dan pengaturan limit pelanggan.</p>
            </div>
            <a href="{{ route('customers.index') }}" class="text-slate-500 hover:text-slate-800 font-medium text-sm flex items-center gap-1 transition-colors">
                &larr; Batal & Kembali
            </a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="p-8 space-y-8">
                    
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">1</span>
                            Identitas Perusahaan / Perorangan
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Customer <span class="text-red-500">*</span></label>
                                <input type="text" name="code" value="{{ old('code', $customer->code) }}" 
                                       class="w-full border-slate-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 font-mono bg-slate-50 text-slate-600 cursor-not-allowed" 
                                       readonly title="Kode customer sebaiknya tidak diubah">
                                @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $customer->name) }}" 
                                       class="w-full border-slate-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 placeholder:text-slate-300" 
                                       required>
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                                <textarea name="address" rows="3" 
                                          class="w-full border-slate-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500 placeholder:text-slate-300">{{ old('address', $customer->address) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-100"></div>

                    <div>
                        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-sm">2</span>
                            Informasi Kontak
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">No. Telepon / WhatsApp</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    </div>
                                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" 
                                           class="w-full pl-10 border-slate-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Email</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <input type="email" name="email" value="{{ old('email', $customer->email) }}" 
                                           class="w-full pl-10 border-slate-300 rounded-lg text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-100"></div>

                    <div>
                        <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">3</span>
                            Pengaturan Keuangan & Limit
                        </h2>
                        <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Termin Pembayaran</label>
                                <select name="payment_terms" class="w-full border-slate-300 rounded-lg text-sm focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="Cash" {{ old('payment_terms', $customer->payment_terms) == 'Cash' ? 'selected' : '' }}>Cash / Tunai</option>
                                    <option value="NET 7" {{ old('payment_terms', $customer->payment_terms) == 'NET 7' ? 'selected' : '' }}>Tempo 7 Hari (NET 7)</option>
                                    <option value="NET 14" {{ old('payment_terms', $customer->payment_terms) == 'NET 14' ? 'selected' : '' }}>Tempo 14 Hari (NET 14)</option>
                                    <option value="NET 30" {{ old('payment_terms', $customer->payment_terms) == 'NET 30' ? 'selected' : '' }}>Tempo 30 Hari (NET 30)</option>
                                    <option value="NET 60" {{ old('payment_terms', $customer->payment_terms) == 'NET 60' ? 'selected' : '' }}>Tempo 60 Hari (NET 60)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Credit Limit (Batas Piutang)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-slate-500 font-bold text-sm">Rp</span>
                                    <input type="number" name="credit_limit" value="{{ old('credit_limit', $customer->credit_limit) }}" 
                                           class="w-full pl-10 border-slate-300 rounded-lg text-sm font-bold text-slate-800 focus:ring-emerald-500 focus:border-emerald-500" 
                                           placeholder="0">
                                </div>
                                <div class="flex items-start gap-1 mt-2">
                                    <svg class="w-4 h-4 text-emerald-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-xs text-slate-500 leading-tight">
                                        Isi <strong>0</strong> jika tidak ada batas (Unlimited).<br>
                                        Mengubah nilai ini akan mempengaruhi validasi order selanjutnya.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="px-8 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-4">
                    <a href="{{ route('customers.index') }}" class="px-6 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-700 font-bold text-sm hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-700 shadow-md transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>