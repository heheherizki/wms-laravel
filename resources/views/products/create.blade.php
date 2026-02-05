<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-slate-900">Tambah Produk Baru</h1>
            <p class="text-slate-500 text-sm mt-1">Isi detail produk untuk inventaris gudang.</p>
        </div>

        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                
                {{-- Bagian 1: Informasi Dasar --}}
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs">1</span>
                        Informasi Produk
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: Lampu LED Philips 12W">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">SKU / Kode Barang <span class="text-red-500">*</span></label>
                            <input type="text" name="sku" required class="w-full rounded-lg border-slate-300 font-mono text-sm" placeholder="UNIK-001">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tipe Item</label>
                            <select name="type" class="w-full rounded-lg border-slate-300 bg-slate-50">
                                <option value="finished_good">📦 Barang Jadi (Jual)</option>
                                <option value="sparepart">⚙️ Sparepart / Komponen</option>
                                <option value="raw_material">🧱 Bahan Baku (Produksi)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Kategori</label>
                            <input type="text" name="category" list="category_list" class="w-full rounded-lg border-slate-300" placeholder="Elektronik">
                            <datalist id="category_list">
                                <option value="Elektronik">
                                <option value="Furniture">
                                <option value="Pakaian">
                            </datalist>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Merk (Brand)</label>
                            <input type="text" name="brand" class="w-full rounded-lg border-slate-300" placeholder="Samsung / LG">
                        </div>
                    </div>
                </div>

                {{-- Bagian 2: Satuan & Lokasi --}}
                <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs">2</span>
                        Satuan & Penyimpanan
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Satuan Terkecil <span class="text-red-500">*</span></label>
                            <select name="unit" class="w-full rounded-lg border-slate-300">
                                <option value="Pcs">Pcs</option>
                                <option value="Unit">Unit</option>
                                <option value="Box">Box</option>
                                <option value="Kg">Kg</option>
                                <option value="Meter">Meter</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Lokasi Rak</label>
                            <input type="text" name="rack_location" class="w-full rounded-lg border-slate-300" placeholder="A-01-05">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Minimum Stok (Alert)</label>
                            <input type="number" name="min_stock" value="5" class="w-full rounded-lg border-slate-300" min="0">
                        </div>
                    </div>

                    {{-- Opsi Kemasan Besar --}}
                    <div class="mt-4 p-4 bg-white border border-slate-200 rounded-lg">
                        <label class="flex items-center gap-2 mb-2 cursor-pointer">
                            <input type="checkbox" x-data="{}" @change="$el.checked ? $refs.packOptions.classList.remove('hidden') : $refs.packOptions.classList.add('hidden')" class="rounded text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm font-bold text-slate-700">Punya Kemasan Besar (Dus/Karton)?</span>
                        </label>
                        
                        <div x-ref="packOptions" class="hidden grid grid-cols-2 gap-4 mt-2">
                            <div>
                                <label class="text-xs text-slate-500">Nama Satuan Besar</label>
                                <input type="text" name="pack_unit" class="w-full rounded-lg border-slate-300 text-sm" placeholder="Contoh: Dus">
                            </div>
                            <div>
                                <label class="text-xs text-slate-500">Isi per Kemasan (Pcs)</label>
                                <input type="number" name="pack_quantity" class="w-full rounded-lg border-slate-300 text-sm" placeholder="Contoh: 12">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bagian 3: Harga (Opsional) --}}
                <div class="p-6">
                    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs">3</span>
                        Harga (Estimasi)
                    </h3>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Harga Beli (HPP)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-slate-500 text-sm">Rp</span>
                                <input type="number" name="buy_price" class="w-full pl-8 rounded-lg border-slate-300" placeholder="0">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Harga Jual</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-slate-500 text-sm">Rp</span>
                                <input type="number" name="sell_price" class="w-full pl-8 rounded-lg border-slate-300" placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer Tombol --}}
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                    <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-700 font-bold text-sm hover:bg-white transition-colors">Batal</a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-md transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Produk
                    </button>
                </div>

            </div>
        </form>
    </div>
</x-app-layout>