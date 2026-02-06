<x-app-layout>
    {{-- Notifikasi Toast (Pojok Kanan Atas) --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" 
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed top-4 right-4 z-[100] max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-gray-900">Berhasil!</p>
                        <p class="mt-1 text-sm text-gray-500">{{ session('success') }}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="show = false" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Root x-data untuk Modal Quick Action --}}
    <div x-data="{ 
        openIn: false, 
        openOut: false, 
        selectedItem: null,
        selectItem(item) { this.selectedItem = item; }
    }">

        {{-- HEADER SECTION --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen Produk</h1>
                <p class="text-slate-500 text-sm">Monitor stok, lokasi rak, dan pergerakan barang.</p>
            </div>
            
            <div class="flex items-center gap-3">
                {{-- Tombol Laporan Cepat (Optional) --}}
                <a href="{{ route('reports.stock') }}" class="hidden md:inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Laporan Stok
                </a>

                @can('create_product')
                <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg shadow-md shadow-indigo-200 transition-all transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Tambah Produk
                </a>
                @endcan
            </div>
        </div>

        {{-- FILTER & TOOLS BAR --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm mb-6 sticky top-20 z-20">
            <div class="p-4 border-b border-slate-100 flex flex-col lg:flex-row justify-between lg:items-center gap-4">
                
                {{-- TABS TYPE --}}
                <div class="flex p-1 bg-slate-100 rounded-lg w-full lg:w-auto">
                    <a href="{{ route('products.index') }}" 
                       class="flex-1 lg:flex-none px-4 py-1.5 text-sm font-medium rounded-md text-center transition-all {{ !request('type') ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                        Semua
                    </a>
                    <a href="{{ route('products.index', ['type' => 'finished_good']) }}" 
                       class="flex-1 lg:flex-none px-4 py-1.5 text-sm font-medium rounded-md text-center transition-all {{ request('type') == 'finished_good' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                       📦 Barang Jadi
                    </a>
                    <a href="{{ route('products.index', ['type' => 'material']) }}" 
                       class="flex-1 lg:flex-none px-4 py-1.5 text-sm font-medium rounded-md text-center transition-all {{ request('type') == 'material' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                       ⚙️ Bahan & Sparepart
                    </a>
                </div>

                {{-- SEARCH BOX --}}
                <form action="{{ route('products.index') }}" method="GET" class="w-full lg:w-96 relative">
                    @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                    
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-lg leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-all" 
                               placeholder="Cari Nama, SKU, atau Merk..."
                               onkeydown="if(event.key === 'Enter'){ this.form.submit(); }">
                    </div>
                </form>
            </div>

            {{-- ADVANCED FILTERS (Collapsible/Row) --}}
            <form action="{{ route('products.index') }}" method="GET" class="p-4 bg-slate-50 rounded-b-xl grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif

                {{-- Filter Brand --}}
                <select name="brand" onchange="this.form.submit()" class="block w-full pl-3 pr-8 py-2 text-xs border-slate-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm">
                    <option value="">Semua Merk</option>
                    @foreach($brands as $brand)
                        <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                    @endforeach
                </select>

                {{-- Filter Lokasi --}}
                <select name="location" onchange="this.form.submit()" class="block w-full pl-3 pr-8 py-2 text-xs border-slate-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm">
                    <option value="">Semua Rak</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>Rak {{ $loc }}</option>
                    @endforeach
                </select>

                {{-- Filter Status --}}
                <select name="status" onchange="this.form.submit()" class="block w-full pl-3 pr-8 py-2 text-xs border-slate-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm">
                    <option value="">Semua Status</option>
                    <option value="normal" {{ request('status') == 'normal' ? 'selected' : '' }}>✅ Stok Aman</option>
                    <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>⚠️ Low Stock</option>
                    <option value="out" {{ request('status') == 'out' ? 'selected' : '' }}>❌ Stok Habis</option>
                </select>

                {{-- Sorting --}}
                <select name="sort" onchange="this.form.submit()" class="block w-full pl-3 pr-8 py-2 text-xs border-slate-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 rounded-lg shadow-sm">
                    <option value="">Urut: Terbaru</option>
                    <option value="lowest" {{ request('sort') == 'lowest' ? 'selected' : '' }}>Stok Terendah</option>
                    <option value="highest" {{ request('sort') == 'highest' ? 'selected' : '' }}>Stok Tertinggi</option>
                </select>

                {{-- Reset Button --}}
                <div class="col-span-2 md:col-span-4 lg:col-span-2 flex justify-end">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center w-full px-4 py-2 border border-slate-300 shadow-sm text-xs font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                        <svg class="w-3 h-3 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Reset Filter
                    </a>
                </div>
            </form>
        </div>

        {{-- MAIN TABLE --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Informasi Produk</th>
                            <th class="px-6 py-4">Tipe & Merk</th>
                            <th class="px-6 py-4">Lokasi</th>
                            <th class="px-6 py-4 text-center">Status Stok</th>
                            <th class="px-6 py-4 text-right">Harga Jual</th>
                            <th class="px-6 py-4 text-center">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($products as $product)
                        @php
                            $isLow = $product->stock <= $product->min_stock && $product->stock > 0;
                            $isOut = $product->stock <= 0;
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors group">
                            
                            {{-- 1. Info Produk --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center font-bold text-lg shrink-0 
                                        {{ $isOut ? 'bg-red-50 text-red-500' : ($isLow ? 'bg-orange-50 text-orange-500' : 'bg-indigo-50 text-indigo-600') }}">
                                        {{ substr($product->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">{{ $product->name }}</div>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="font-mono text-[10px] text-slate-500 bg-slate-100 px-1.5 rounded border border-slate-200">{{ $product->sku }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- 2. Kategori --}}
                            <td class="px-6 py-4">
                                <div class="text-slate-700 font-medium text-xs">{{ $product->brand ?? 'No Brand' }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $product->category }}</div>
                            </td>

                            {{-- 3. Lokasi --}}
                            <td class="px-6 py-4">
                                @if($product->rack_location)
                                    <div class="flex items-center gap-1.5 text-slate-600">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span class="font-mono text-xs">{{ $product->rack_location }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-300 text-xs italic">-</span>
                                @endif
                            </td>

                            {{-- 4. Stok --}}
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="text-sm font-bold {{ $isOut ? 'text-red-600' : ($isLow ? 'text-orange-500' : 'text-slate-800') }}">
                                        {{ number_format($product->stock) }} <span class="text-xs font-normal text-slate-500">{{ $product->unit }}</span>
                                    </span>
                                    
                                    {{-- Status Badge --}}
                                    @if($isOut)
                                        <span class="mt-1 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">
                                            HABIS
                                        </span>
                                    @elseif($isLow)
                                        <span class="mt-1 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-700">
                                            KRITIS (Min: {{ $product->min_stock }})
                                        </span>
                                    @else
                                        <span class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700">
                                            AMAN
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- 5. Harga --}}
                            <td class="px-6 py-4 text-right">
                                <div class="font-mono text-slate-700 text-xs">
                                    Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                                </div>
                            </td>

                            {{-- 6. Aksi --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2 opacity-100 md:opacity-60 group-hover:opacity-100 transition-opacity">
                                    
                                    {{-- Quick IN --}}
                                    <button @click="openIn = true; selectItem({{ $product }})" 
                                            class="p-1.5 text-green-600 hover:bg-green-50 rounded border border-slate-200 hover:border-green-200 transition-colors" 
                                            title="Stok Masuk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </button>

                                    {{-- Quick OUT --}}
                                    <button @click="openOut = true; selectItem({{ $product }})" 
                                            class="p-1.5 text-red-600 hover:bg-red-50 rounded border border-slate-200 hover:border-red-200 transition-colors" 
                                            title="Stok Keluar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                    </button>

                                    {{-- Edit Page --}}
                                    <a href="{{ route('products.edit', $product->id) }}" 
                                       class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded border border-slate-200 hover:border-indigo-200 transition-colors" 
                                       title="Edit Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>

                                    {{-- Barcode --}}
                                    <a href="{{ route('products.barcode', $product->id) }}" target="_blank"
                                       class="p-1.5 text-slate-500 hover:bg-slate-100 rounded border border-slate-200 transition-colors" 
                                       title="Cetak Barcode">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5v14M7 5v14M11 5v14M15 5v14M17 5v14M21 5v14" /></svg>
                                    </a>

                                    {{-- Tombol History --}}
                                    <a href="{{ route('products.history', $product->id) }}" 
                                        class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded border border-slate-200 hover:border-blue-200 transition-colors" 
                                        title="Lihat Kartu Stok">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </a>

                                </div>
                            </td>
                        </tr>
                        @empty
                        {{-- Empty State Premium --}}
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center bg-slate-50/50">
                                <div class="flex flex-col items-center justify-center max-w-md mx-auto">
                                    <div class="bg-white p-4 rounded-full shadow-sm mb-4">
                                        <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <h3 class="text-slate-900 font-bold text-lg">Produk Tidak Ditemukan</h3>
                                    <p class="text-slate-500 text-sm mt-1 mb-6">Kami tidak dapat menemukan produk yang cocok dengan filter pencarian Anda. Coba kata kunci lain atau tambahkan produk baru.</p>
                                    <a href="{{ route('products.index') }}" class="text-indigo-600 font-medium hover:text-indigo-700 text-sm hover:underline">
                                        Reset Filter Pencarian
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Pagination Footer --}}
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $products->links() }}
            </div>
        </div>

        {{-- ================= MODAL BARANG MASUK (QUICK IN) ================= --}}
        <div x-show="openIn" class="fixed inset-0 z-[80] overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" @click="openIn = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-green-600 px-4 py-3 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Barang Masuk (Quick In)
                        </h3>
                        <button @click="openIn = false" class="text-green-100 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                    </div>

                    <form action="{{ route('products.in') }}" method="POST" class="p-6">
                        @csrf
                        <input type="hidden" name="product_id" x-bind:value="selectedItem ? selectedItem.id : ''">
                        
                        <div class="mb-4">
                            <p class="text-sm text-slate-500">Menambah stok untuk produk:</p>
                            <div class="font-bold text-slate-900 text-lg" x-text="selectedItem ? selectedItem.name : ''"></div>
                            <div class="text-xs font-mono text-slate-500" x-text="selectedItem ? selectedItem.sku : ''"></div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jumlah</label>
                                <input type="number" name="quantity" required min="1" class="w-full rounded-lg border-slate-300 focus:ring-green-500 focus:border-green-500" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Satuan</label>
                                <select name="unit" class="w-full rounded-lg border-slate-300 bg-slate-50">
                                    <option value="pcs">Pcs (Satuan Kecil)</option>
                                    <template x-if="selectedItem && selectedItem.pack_quantity > 1">
                                        <option value="pack">
                                            <span x-text="selectedItem.pack_unit ? selectedItem.pack_unit : 'Dus'"></span>
                                            (Isi <span x-text="selectedItem.pack_quantity"></span>)
                                        </option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Sumber / Supplier</label>
                            <select name="reference" class="w-full rounded-lg border-slate-300 focus:ring-green-500 focus:border-green-500">
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->name }}">{{ $sup->name }}</option>
                                @endforeach
                                <option value="Stok Opname">Stok Opname (Penyesuaian)</option>
                                <option value="Retur Customer">Retur Customer</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan</label>
                            <textarea name="notes" rows="2" class="w-full rounded-lg border-slate-300 focus:ring-green-500 focus:border-green-500"></textarea>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button @click="openIn = false" type="button" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold shadow-md">Simpan Masuk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================= MODAL BARANG KELUAR (QUICK OUT) ================= --}}
        <div x-show="openOut" class="fixed inset-0 z-[80] overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" @click="openOut = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <div class="bg-red-600 px-4 py-3 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                            Barang Keluar (Quick Out)
                        </h3>
                        <button @click="openOut = false" class="text-red-100 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                    </div>

                    <form action="{{ route('products.out') }}" method="POST" class="p-6">
                        @csrf
                        <input type="hidden" name="product_id" x-bind:value="selectedItem ? selectedItem.id : ''">
                        
                        <div class="mb-4">
                            <p class="text-sm text-slate-500">Mengurangi stok dari produk:</p>
                            <div class="font-bold text-slate-900 text-lg" x-text="selectedItem ? selectedItem.name : ''"></div>
                            <div class="text-xs font-mono text-slate-500" x-text="selectedItem ? selectedItem.sku : ''"></div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jumlah</label>
                                <input type="number" name="quantity" required min="1" class="w-full rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Satuan</label>
                                <select name="unit" class="w-full rounded-lg border-slate-300 bg-slate-50">
                                    <option value="pcs">Pcs (Satuan Kecil)</option>
                                    <template x-if="selectedItem && selectedItem.pack_quantity > 1">
                                        <option value="pack">
                                            <span x-text="selectedItem.pack_unit ? selectedItem.pack_unit : 'Dus'"></span>
                                            (Isi <span x-text="selectedItem.pack_quantity"></span>)
                                        </option>
                                    </template>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tujuan / Customer</label>
                            <select name="reference" class="w-full rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                                <option value="">-- Pilih Tujuan --</option>
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->name }}">{{ $cust->name }}</option>
                                @endforeach
                                <option value="Internal Use">Pemakaian Internal (Rusak/Hilang)</option>
                                <option value="Retur Supplier">Retur Ke Supplier</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan</label>
                            <textarea name="notes" rows="2" class="w-full rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500"></textarea>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button @click="openOut = false" type="button" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold shadow-md">Simpan Keluar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>