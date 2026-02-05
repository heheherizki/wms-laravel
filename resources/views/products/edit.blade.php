<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Edit Produk</h1>
                <p class="text-slate-500 text-sm mt-1">Perbarui data: <span class="font-bold text-indigo-600">{{ $product->name }}</span></p>
            </div>
            
            {{-- Tombol Hapus (Hanya Admin) --}}
            @role('admin|super_admin')
            <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Yakin hapus produk ini?');">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-bold transition-colors">
                    Hapus Produk
                </button>
            </form>
            @endrole
        </div>

        <form action="{{ route('products.update', $product->id) }}" method="POST">
            @csrf @method('PUT')
            
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                
                {{-- FORM SAMA DENGAN CREATE, TAPI DENGAN VALUE --}}
                <div class="p-6 border-b border-slate-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nama Produk</label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full rounded-lg border-slate-300">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required class="w-full rounded-lg border-slate-300 bg-slate-50 font-mono text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tipe Item</label>
                            <select name="type" class="w-full rounded-lg border-slate-300">
                                <option value="finished_good" {{ $product->type == 'finished_good' ? 'selected' : '' }}>Barang Jadi</option>
                                <option value="sparepart" {{ $product->type == 'sparepart' ? 'selected' : '' }}>Sparepart</option>
                                <option value="raw_material" {{ $product->type == 'raw_material' ? 'selected' : '' }}>Bahan Baku</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Kategori</label>
                            <input type="text" name="category" value="{{ old('category', $product->category) }}" class="w-full rounded-lg border-slate-300">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Merk</label>
                            <input type="text" name="brand" value="{{ old('brand', $product->brand) }}" class="w-full rounded-lg border-slate-300">
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-slate-50/50">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Satuan</label>
                            <select name="unit" class="w-full rounded-lg border-slate-300">
                                <option value="Pcs" {{ $product->unit == 'Pcs' ? 'selected' : '' }}>Pcs</option>
                                <option value="Unit" {{ $product->unit == 'Unit' ? 'selected' : '' }}>Unit</option>
                                <option value="Set" {{ $product->unit == 'Set' ? 'selected' : '' }}>Set</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Lokasi Rak</label>
                            <input type="text" name="rack_location" value="{{ old('rack_location', $product->rack_location) }}" class="w-full rounded-lg border-slate-300">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Min. Stok</label>
                            <input type="number" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" class="w-full rounded-lg border-slate-300">
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-slate-100">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Harga Beli</label>
                            <input type="number" name="buy_price" value="{{ old('buy_price', $product->buy_price) }}" class="w-full rounded-lg border-slate-300">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Harga Jual</label>
                            <input type="number" name="sell_price" value="{{ old('sell_price', $product->sell_price) }}" class="w-full rounded-lg border-slate-300">
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end gap-3">
                    <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-lg border border-slate-300 text-slate-700 font-bold text-sm hover:bg-white">Batal</a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-md transition-all">
                        Simpan Perubahan
                    </button>
                </div>

            </div>
        </form>
    </div>
</x-app-layout>