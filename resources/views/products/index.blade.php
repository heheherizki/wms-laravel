<x-app-layout>
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition.opacity.duration.500ms
             x-init="setTimeout(() => show = false, 4000)" 
             @click="show = false"
             class="fixed top-5 right-5 z-[60] bg-green-500 text-white px-6 py-3 rounded-lg shadow-xl flex items-center gap-3 cursor-pointer hover:bg-green-600 transition-colors">
            
            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <div>
                <h4 class="font-bold text-sm">Berhasil!</h4>
                <p class="text-xs opacity-90">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition.opacity.duration.500ms
             x-init="setTimeout(() => show = false, 5000)" 
             @click="show = false"
             class="fixed top-5 right-5 z-[60] bg-red-500 text-white px-6 py-3 rounded-lg shadow-xl flex items-center gap-3 cursor-pointer hover:bg-red-600 transition-colors">
            
            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div>
                <h4 class="font-bold text-sm">Gagal!</h4>
                <p class="text-xs opacity-90">{{ session('error') }}</p>
                <p class="text-[10px] mt-1 italic opacity-75">(Klik untuk tutup)</p>
            </div>
        </div>
    @endif

    <div x-data="{ 
        openIn: false, 
        openOut: false, 
        openHistory: false,
        openEdit: false,
        openCreate: false,
        selectedItem: null,
        historyData: [],
        isLoadingHistory: false,
        
        // State untuk Filter History
        filterType: '',
        filterSearch: '',
        filterDateStart: '',
        filterDateEnd: '',

        selectItem(item) {
            this.selectedItem = item;
        },

        fetchHistory(product) {
            this.selectedItem = product;
            this.openHistory = true;
            this.isLoadingHistory = true;
            this.historyData = [];
            
            // Reset Filter saat buka modal baru
            this.filterType = '';
            this.filterSearch = '';
            this.filterDateStart = '';
            this.filterDateEnd = '';

            fetch('/products/' + product.id + '/history')
                .then(response => {
                    if (!response.ok) throw new Error('Network response');
                    return response.json();
                })
                .then(data => {
                    this.historyData = data;
                })
                .catch(error => {
                    console.error('Gagal ambil history:', error);
                })
                .finally(() => {
                    this.isLoadingHistory = false;
                });
        },

        // LOGIKA FILTER CANGGIH (Computed Property ala Alpine)
        get filteredHistory() {
            return this.historyData.filter(log => {
                // 1. Filter Tipe
                if (this.filterType && log.type !== this.filterType) return false;
                
                // 2. Filter Search (Cari di Ref / Notes / Nama User)
                if (this.filterSearch) {
                    const search = this.filterSearch.toLowerCase();
                    const ref = (log.reference || '').toLowerCase();
                    const notes = (log.notes || '').toLowerCase();
                    const user = (log.user ? log.user.name : 'System').toLowerCase();
                    
                    if (!ref.includes(search) && !notes.includes(search) && !user.includes(search)) return false;
                }

                // 3. Filter Tanggal
                const logDate = log.created_at.substring(0, 10); // Ambil YYYY-MM-DD
                if (this.filterDateStart && logDate < this.filterDateStart) return false;
                if (this.filterDateEnd && logDate > this.filterDateEnd) return false;

                return true;
            });
        }
    }">

        <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Stok Gudang</h1>
                <p class="text-slate-500 mt-1">Monitoring stok masuk dan keluar.</p>
            </div>

            @if(Auth::user()->role === 'admin')
            <button @click="openCreate = true" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                </svg>
                Produk Baru
            </button>
            @endif
            
        </div>

        <div class="mb-6 border-b border-slate-200">
            <nav class="-mb-px flex space-x-8">
                <a href="{{ route('products.index') }}" class="{{ !request('type') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Semua Item
                </a>
                <a href="{{ route('products.index', ['type' => 'finished_good']) }}" class="{{ request('type') == 'finished_good' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                📦 Barang Jadi
                </a>
                <a href="{{ route('products.index', ['type' => 'material']) }}" class="{{ request('type') == 'material' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                ⚙️ Sparepart & Bahan
                </a>
            </nav>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm mb-6">
            <form action="{{ route('products.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4">
                    <div class="lg:col-span-4 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            class="pl-10 block w-full rounded-lg border-slate-200 bg-slate-50 text-slate-800 text-sm focus:bg-white focus:border-red-500 focus:ring-red-500 transition-colors" 
                            placeholder="Cari nama barang, SKU...">
                    </div>
                    <div class="lg:col-span-2">
                        <select name="brand" onchange="this.form.submit()" class="block w-full rounded-lg border-slate-200 bg-slate-50 text-slate-700 text-sm focus:bg-white focus:border-red-500 focus:ring-red-500">
                            <option value="">Semua Merk</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="lg:col-span-2">
                        <select name="location" onchange="this.form.submit()" class="block w-full rounded-lg border-slate-200 bg-slate-50 text-slate-700 text-sm focus:bg-white focus:border-red-500 focus:ring-red-500">
                            <option value="">Semua Lokasi</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>Rak {{ $loc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="lg:col-span-2">
                        <select name="status" onchange="this.form.submit()" class="block w-full rounded-lg border-slate-200 bg-slate-50 text-slate-700 text-sm focus:bg-white focus:border-red-500 focus:ring-red-500">
                            <option value="">Semua Status</option>
                            <option value="normal" {{ request('status') == 'normal' ? 'selected' : '' }}>✅ Stok Aman</option>
                            <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>⚠️ Low Stock</option>
                            <option value="habis" {{ request('status') == 'habis' ? 'selected' : '' }}>❌ Habis (0)</option>
                        </select>
                    </div>
                    <div class="lg:col-span-2">
                        <select name="sort" onchange="this.form.submit()" class="block w-full rounded-lg border-slate-200 bg-slate-50 text-slate-700 text-sm focus:bg-white focus:border-red-500 focus:ring-red-500">
                            <option value="">Urutan: Terbaru</option>
                            <option value="lowest" {{ request('sort') == 'lowest' ? 'selected' : '' }}>Stok Terendah</option>
                            <option value="highest" {{ request('sort') == 'highest' ? 'selected' : '' }}>Stok Tertinggi</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col max-h-[75vh]">
            <div class="overflow-auto relative flex-grow">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-slate-100 text-slate-600 font-bold sticky top-0 z-20 shadow-sm">
                        <tr>
                            <th class="px-6 py-4 w-12 text-center bg-slate-100">#</th>
                            <th class="px-6 py-4 bg-slate-100">Produk</th>
                            <th class="px-6 py-4 bg-slate-100">Tipe & Harga</th>
                            <th class="px-6 py-4 bg-slate-100">Kemasan</th>
                            <th class="px-6 py-4 bg-slate-100">Lokasi</th>
                            <th class="px-6 py-4 bg-slate-100">Stok</th>
                            <th class="px-6 py-4 text-center sticky right-0 z-30 bg-slate-100 shadow-[-4px_0_8px_-2px_rgba(0,0,0,0.05)] w-48">
                                Quick Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($products as $product)
                        @php
                            $isLowStock = $product->stock <= $product->min_stock;
                            $rowClass = $isLowStock ? 'bg-red-50 hover:bg-red-100' : 'bg-white hover:bg-slate-50';
                            $stickyClass = $isLowStock ? 'bg-red-50 group-hover:bg-red-100' : 'bg-white group-hover:bg-slate-50';
                        @endphp

                        <tr class="{{ $rowClass }} transition-colors group">
                            <td class="px-6 py-4 text-slate-400 text-center">{{ $products->firstItem() + $loop->index }}</td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-start gap-3 min-w-[250px]">
                                    <div class="w-10 h-10 rounded-lg {{ $isLowStock ? 'bg-red-200 text-red-600' : 'bg-slate-100 text-slate-400' }} border border-transparent flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                    </div>
                                    <div>
                                        <div class="font-bold {{ $isLowStock ? 'text-red-900' : 'text-slate-900' }}">{{ $product->name }}</div>
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-mono bg-white/50 text-slate-600 border border-slate-200 shadow-sm">{{ $product->sku }}</span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-white/50 text-slate-600 border border-slate-200 shadow-sm">{{ $product->brand }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="mb-1">
                                    @if($product->type == 'finished_good')
                                        <span class="bg-green-100 text-green-800 text-[10px] px-2 py-0.5 rounded-full font-bold">Barang Jadi</span>
                                    @elseif($product->type == 'sparepart')
                                        <span class="bg-slate-200 text-slate-800 text-[10px] px-2 py-0.5 rounded-full font-bold">Sparepart</span>
                                    @else
                                        <span class="bg-yellow-100 text-yellow-800 text-[10px] px-2 py-0.5 rounded-full font-bold">Bahan Baku</span>
                                    @endif
                                </div>
                                <div class="text-xs text-slate-500">Beli: Rp {{ number_format($product->buy_price) }}</div>
                                <div class="text-xs font-bold text-indigo-600">Jual: Rp {{ number_format($product->sell_price) }}</div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->pack_quantity)
                                    <div><span class="text-slate-900 font-semibold">1 {{ $product->pack_unit }}</span> <span class="text-slate-400">=</span> {{ $product->pack_quantity }} {{ $product->unit }}</div>
                                @else
                                    <span class="text-slate-400 text-xs italic">Satuan Tunggal</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-slate-600 bg-slate-100 px-2 py-1 rounded border border-slate-200">{{ $product->rack_location ?? '-' }}</span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold {{ $isLowStock ? 'text-red-700' : 'text-slate-900' }} text-base">
                                    {{ $product->stock_label }}
                                </div>
                                
                                @if($isLowStock)
                                    <div class="flex items-center gap-1 mt-1 text-xs text-red-600 font-bold bg-red-100 px-2 py-0.5 rounded w-fit">
                                        <svg class="w-3 h-3 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        Min: {{ $product->min_stock_label }}
                                    </div>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center sticky right-0 z-10 shadow-[-4px_0_8px_-2px_rgba(0,0,0,0.05)] {{ $stickyClass }}">
                                <div class="flex justify-center items-center gap-1">
                                    
                                    <button @click="openIn = true; selectItem({{ $product }})" 
                                        class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-green-50 hover:text-green-700 hover:border-green-200 transition-all text-slate-400" title="Barang Masuk">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    </button>

                                    <button @click="openOut = true; selectItem({{ $product }})"
                                        class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-red-50 hover:text-red-700 hover:border-red-200 transition-all text-slate-400" title="Barang Keluar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                    </button>

                                    <button @click="fetchHistory({{ $product }})" 
                                        class="p-2 text-slate-400 hover:text-blue-600 hover:bg-white rounded-lg transition-colors border border-transparent hover:border-slate-200" title="Riwayat Stok">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>

                                    @if(Auth::user()->role === 'admin')
                                        <button @click="openEdit = true; selectItem({{ $product }})" 
                                            class="p-2 text-slate-400 hover:text-orange-600 hover:bg-white rounded-lg transition-colors border border-transparent hover:border-slate-200" title="Edit Barang">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>  
                                    @else
                                        <span class="p-2 text-slate-300 cursor-not-allowed" title="Akses Admin Diperlukan">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        </span>
                                    @endif         
                                    
                                    <a href="{{ route('products.barcode', $product->id) }}?qty=6" 
                                        target="_blank"
                                        class="text-slate-400 hover:text-slate-800 transition-colors" 
                                        title="Cetak Label Barcode">
                                        
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4h2v-4zM6 8v5a3 3 0 003 3h1m0-4h2m4 0h2m-4-8h2m-2 4h2m-7 0H9m-2 4H5a2 2 0 01-2-2V9a2 2 0 012-2h2a2 2 0 012 2v2m7 11v1m0-11h2"></path></svg>
                                    </a>

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="py-12 text-center text-slate-500">Data tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 sticky bottom-0 z-20">{{ $products->links() }}</div>
        </div>

        <div x-show="openIn" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-75" @click="openIn = false"></div>
                
                <form action="{{ route('products.in') }}" method="POST" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    @csrf
                    <input type="hidden" name="product_id" x-bind:value="selectedItem ? selectedItem.id : ''">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-slate-900 mb-2">🟢 Terima Barang Masuk</h3>
                        <p class="text-sm text-slate-500 mb-4">Menambah stok untuk <span class="font-bold text-slate-800" x-text="selectedItem ? selectedItem.name : ''"></span></p>
                        
                        <div class="space-y-4" x-data="{ unitType: 'pcs' }">
                            
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Jumlah & Satuan</label>
                                <div class="flex mt-1">
                                    <input type="number" name="quantity" step="0.01" required min="0.01" 
                                        class="block w-full rounded-l-lg border-slate-300 shadow-sm focus:border-green-500 focus:ring-green-200 sm:text-sm" 
                                        placeholder="0">
                                    
                                    <select name="unit" x-model="unitType" class="inline-flex items-center px-2 rounded-r-lg border border-l-0 border-slate-300 bg-slate-50 text-slate-700 text-sm font-medium focus:ring-green-500 focus:border-green-500">
                                        <option value="pcs">Pcs (Satuan Kecil)</option>
                                        <template x-if="selectedItem && selectedItem.pack_quantity > 1">
                                            <option value="pack">
                                                <span x-text="selectedItem.pack_unit ? selectedItem.pack_unit : 'Dus'"></span>
                                                (Isi <span x-text="selectedItem.pack_quantity"></span>)
                                            </option>
                                        </template>
                                    </select>
                                </div>
                                <template x-if="unitType === 'pack' && selectedItem">
                                    <p class="text-xs text-green-600 mt-1 font-medium">
                                        Info: Sistem akan otomatis mencatat masuk <span class="font-bold">x <span x-text="selectedItem.pack_quantity"></span> Pcs</span>.
                                    </p>
                                </template>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Sumber / Supplier</label>
                                <div class="relative">
                                    <select name="reference" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-green-500 focus:ring-green-200 sm:text-sm">
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach($suppliers as $sup)
                                            <option value="{{ $sup->name }}">{{ $sup->name }}</option>
                                        @endforeach
                                        <option value="Lainnya">Lainnya / Stok Opname</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="mt-2 block text-xs font-bold text-slate-700 uppercase">Keterangan (Optional)</label>
                                    <textarea name="notes" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-200 sm:text-sm" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm">Simpan Masuk</button>
                        <button @click="openIn = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openOut" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-75" @click="openOut = false"></div>
                
                <form action="{{ route('products.out') }}" method="POST" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    @csrf
                    <input type="hidden" name="product_id" x-bind:value="selectedItem ? selectedItem.id : ''">

                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-slate-900 mb-2">🔴 Catat Barang Keluar</h3>
                        <p class="text-sm text-slate-500 mb-4">Mengurangi stok dari <span class="font-bold text-slate-800" x-text="selectedItem ? selectedItem.name : ''"></span></p>
                        
                        <div class="space-y-4" x-data="{ unitType: 'pcs' }">
                            
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Jumlah & Satuan</label>
                                <div class="flex mt-1">
                                    <input type="number" name="quantity" step="0.01" required min="0.01" 
                                        class="block w-full rounded-l-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-200 sm:text-sm" 
                                        placeholder="0">
                                    
                                    <select name="unit" x-model="unitType" class="inline-flex items-center px-2 rounded-r-lg border border-l-0 border-slate-300 bg-slate-50 text-slate-700 text-sm font-medium focus:ring-red-500 focus:border-red-500">
                                        <option value="pcs">Pcs (Satuan Kecil)</option>
                                        
                                        <template x-if="selectedItem && selectedItem.pack_quantity > 1">
                                            <option value="pack">
                                                <span x-text="selectedItem.pack_unit ? selectedItem.pack_unit : 'Dus'"></span>
                                                (Isi <span x-text="selectedItem.pack_quantity"></span>)
                                            </option>
                                        </template>
                                    </select>
                                </div>
                                
                                <template x-if="unitType === 'pack' && selectedItem">
                                    <p class="text-xs text-red-600 mt-1 font-medium">
                                        Info: Stok berkurang <span class="font-bold">x <span x-text="selectedItem.pack_quantity"></span> Pcs</span>.
                                    </p>
                                </template>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Tujuan / Customer</label>
                                <div class="relative">
                                    <select name="reference" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-200 sm:text-sm">
                                        <option value="">-- Pilih Customer --</option>
                                        @foreach($customers as $cust)
                                            <option value="{{ $cust->name }}">{{ $cust->name }}</option>
                                        @endforeach
                                        <option value="Internal Use">Pemakaian Internal (Rusak/Hilang)</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase">Keterangan (Optional)</label>
                                <textarea name="notes" class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-red-500 focus:ring-red-200 sm:text-sm" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Simpan Keluar</button>
                        <button @click="openOut = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="openHistory" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-75" @click="openHistory = false"></div>
                
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">⏳ Riwayat Transaksi</h3>
                                <p class="text-sm text-slate-500">Produk: <span class="font-bold text-slate-800" x-text="selectedItem ? selectedItem.name : ''"></span></p>
                            </div>
                            <button @click="openHistory = false" class="text-slate-400 hover:text-slate-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        
                        <div class="bg-slate-50 p-3 rounded-lg border border-slate-200 mb-4 grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div>
                                <label class="text-[10px] uppercase font-bold text-slate-500">Dari Tanggal</label>
                                <input type="date" x-model="filterDateStart" class="block w-full text-xs rounded border-slate-300">
                            </div>
                            <div>
                                <label class="text-[10px] uppercase font-bold text-slate-500">Sampai Tanggal</label>
                                <input type="date" x-model="filterDateEnd" class="block w-full text-xs rounded border-slate-300">
                            </div>
                            <div>
                                <label class="text-[10px] uppercase font-bold text-slate-500">Tipe</label>
                                <select x-model="filterType" class="block w-full text-xs rounded border-slate-300">
                                    <option value="">Semua</option>
                                    <option value="in">Barang Masuk</option>
                                    <option value="out">Barang Keluar</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] uppercase font-bold text-slate-500">Cari Info/User</label>
                                <input type="text" x-model="filterSearch" placeholder="Cari kata..." class="block w-full text-xs rounded border-slate-300">
                            </div>
                        </div>

                        <div class="overflow-hidden border border-slate-200 rounded-lg max-h-96 overflow-y-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-slate-50 sticky top-0">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Waktu</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">User</th> <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tipe</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Jumlah</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Info & Ref</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-200">
                                    <template x-for="log in filteredHistory" :key="log.id">
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-3 text-xs text-slate-500 whitespace-nowrap">
                                                <div x-text="new Date(log.created_at).toLocaleDateString('id-ID')"></div>
                                                <div class="text-[10px] text-slate-400" x-text="new Date(log.created_at).toLocaleTimeString('id-ID')"></div>
                                            </td>

                                            <td class="px-4 py-3 text-xs text-slate-700 font-medium whitespace-nowrap">
                                                <span x-text="log.user ? log.user.name : 'System'"></span>
                                            </td>
                                            
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                      :class="log.type === 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                                      x-text="log.type === 'in' ? 'Masuk' : 'Keluar'">
                                                </span>
                                            </td>

                                            <td class="px-4 py-3 text-sm font-bold text-slate-700 whitespace-nowrap">
                                                <span x-text="log.type === 'in' ? '+' : '-'"></span>
                                                <span x-text="log.quantity"></span>
                                            </td>

                                            <td class="px-4 py-3 text-xs text-slate-600">
                                                <div class="font-medium text-indigo-600" x-text="log.reference"></div>
                                                <div class="text-slate-400 italic" x-text="log.notes"></div>
                                            </td>
                                        </tr>
                                    </template>
                                    
                                    <tr x-show="filteredHistory.length === 0">
                                        <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
                                            Data tidak ditemukan sesuai filter.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-75" @click="openEdit = false"></div>
                
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    
                    <form id="updateForm" x-bind:action="'/products/' + (selectedItem ? selectedItem.id : '')" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-bold text-slate-900 mb-2">✏️ Edit Produk</h3>
                            <p class="text-sm text-slate-500 mb-4">Mengubah data untuk <span class="font-bold text-slate-800" x-text="selectedItem ? selectedItem.name : ''"></span></p>
                            
                            <div class="space-y-4">
                                
                                <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-100">
                                    <label class="block text-xs font-bold text-indigo-800 uppercase mb-2">Pengaturan ERP & Harga</label>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-600">Tipe Item</label>
                                            <select name="type" x-bind:value="selectedItem ? selectedItem.type : ''" class="w-full rounded border-slate-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                                <option value="finished_good">Barang Jadi</option>
                                                <option value="sparepart">Sparepart</option>
                                                <option value="raw_material">Bahan Baku</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-600">Harga Beli</label>
                                            <input type="number" name="buy_price" x-bind:value="selectedItem ? selectedItem.buy_price : 0" class="w-full rounded border-slate-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-600">Harga Jual</label>
                                            <input type="number" name="sell_price" x-bind:value="selectedItem ? selectedItem.sell_price : 0" class="w-full rounded border-slate-300 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase">Nama Produk</label>
                                    <input type="text" name="name" x-bind:value="selectedItem ? selectedItem.name : ''" required 
                                        class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-200 sm:text-sm">
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase">SKU / Kode</label>
                                        <input type="text" name="sku" x-bind:value="selectedItem ? selectedItem.sku : ''" required 
                                            class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-200 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase">Kategori</label>
                                        <input type="text" name="category" x-bind:value="selectedItem ? selectedItem.category : ''" required 
                                            class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-200 sm:text-sm">
                                    </div>
                                </div>

                                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Konversi Kemasan (Master Data)</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-xs text-slate-500">Nama Satuan Besar</label>
                                            <input type="text" name="pack_unit" x-bind:value="selectedItem ? selectedItem.pack_unit : ''" 
                                                class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-200 sm:text-sm" 
                                                placeholder="Dus / Koli">
                                        </div>
                                        <div>
                                            <label class="text-xs text-slate-500">Isi per Kemasan (Pcs)</label>
                                            <input type="number" name="pack_quantity" x-bind:value="selectedItem ? selectedItem.pack_quantity : ''" 
                                                class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-200 sm:text-sm" 
                                                placeholder="Contoh: 24">
                                        </div>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-1 italic">*Kosongkan jika barang ini hanya satuan Pcs.</p>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div x-data="{ minUnit: 'pcs' }">
                                        <label class="block text-xs font-bold text-slate-700 uppercase">Minimum Stok</label>
                                        <div class="flex mt-1">
                                            <input type="number" name="min_stock" x-bind:value="selectedItem ? selectedItem.min_stock : 0" required min="0" 
                                                class="block w-full rounded-l-lg border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-200 sm:text-sm">
                                            
                                            <select name="min_stock_unit" x-model="minUnit" class="inline-flex items-center px-2 rounded-r-lg border border-l-0 border-slate-300 bg-slate-50 text-slate-700 text-sm font-medium focus:ring-orange-500 focus:border-orange-500">
                                                <option value="pcs">Pcs</option>
                                                <template x-if="selectedItem && selectedItem.pack_quantity > 1">
                                                    <option value="pack">
                                                        <span x-text="selectedItem.pack_unit ? selectedItem.pack_unit : 'Dus'"></span>
                                                    </option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 uppercase">Lokasi Rak</label>
                                        <input type="text" name="rack_location" x-bind:value="selectedItem ? selectedItem.rack_location : ''" 
                                            class="mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-200 sm:text-sm" placeholder="Contoh: A-01-01">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 flex flex-col-reverse sm:flex-row sm:justify-between items-center gap-3">
                        
                        <form x-bind:action="'/products/' + (selectedItem ? selectedItem.id : '')" method="POST" 
                            onsubmit="return confirm('⚠️ PERINGATAN HAPUS \n\nApakah Anda yakin ingin menghapus produk ini?\nProduk yang dihapus akan masuk arsip sampah.')"
                            class="w-full sm:w-auto text-center sm:text-left">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium underline px-2 py-2 hover:bg-red-50 rounded transition-colors">
                                Hapus Produk Ini
                            </button>
                        </form>

                        <div class="flex flex-row gap-3 w-full sm:w-auto justify-end">
                            <button @click="openEdit = false" type="button" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 sm:text-sm">
                                Batal
                            </button>
                            
                            <button type="submit" form="updateForm" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 sm:text-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div x-show="openCreate" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900 bg-opacity-75" @click="openCreate = false"></div>

                <form action="{{ route('products.store') }}" method="POST" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
                    @csrf
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-bold text-slate-900 mb-4">✨ Tambah Produk Baru</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="space-y-4">
                                <div class="mb-4 p-3 bg-indigo-50 rounded-lg border border-indigo-100">
                                    <label class="block text-xs font-bold text-indigo-800 uppercase mb-1">Pengaturan ERP</label>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-600">Tipe Item</label>
                                            <select name="type" class="w-full rounded border-slate-300 text-xs">
                                                <option value="finished_good">Barang Jadi</option>
                                                <option value="sparepart">Sparepart</option>
                                                <option value="raw_material">Bahan Baku</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-600">Harga Beli</label>
                                            <input type="number" name="buy_price" value="0" class="w-full rounded border-slate-300 text-xs">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-600">Harga Jual</label>
                                            <input type="number" name="sell_price" value="0" class="w-full rounded border-slate-300 text-xs">
                                        </div>
                                    </div>
                                </div>                                
                                <div>
                                    <label class="block font-bold text-slate-700">Nama Produk *</label>
                                    <input type="text" name="name" required class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: Lampu LED 9Watt">
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700">SKU / Kode Barang *</label>
                                    <input type="text" name="sku" required class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Unik (Barcode)">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block font-bold text-slate-700">Kategori *</label>
                                        <input type="text" name="category" required class="w-full rounded-lg border-slate-300" placeholder="Lampu/Kabel">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700">Merk (Brand)</label>
                                        <input type="text" name="brand" class="w-full rounded-lg border-slate-300" placeholder="Stark/Visalux">
                                    </div>
                                </div>
                                <div>
                                    <label class="block font-bold text-slate-700">Watt / Spesifikasi</label>
                                    <input type="text" name="watt" class="w-full rounded-lg border-slate-300" placeholder="Opsional (misal: 15 Watt)">
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block font-bold text-slate-700">Satuan Kecil *</label>
                                        <select name="unit" class="w-full rounded-lg border-slate-300">
                                            <option value="Pcs">Pcs</option>
                                            <option value="Unit">Unit</option>
                                            <option value="Set">Set</option>
                                            <option value="Batang">Batang</option>
                                            <option value="Meter">Meter</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700">Lokasi Rak</label>
                                        <input type="text" name="rack_location" class="w-full rounded-lg border-slate-300" placeholder="A-01-01">
                                    </div>
                                </div>

                                <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg">
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Setting Satuan Besar (Dus)</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <input type="text" name="pack_unit" class="w-full rounded-lg border-slate-300 text-xs" placeholder="Nama (Dus)">
                                        </div>
                                        <div>
                                            <input type="number" name="pack_quantity" class="w-full rounded-lg border-slate-300 text-xs" placeholder="Isi (Pcs)">
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700">Minimum Stok (Buffer)</label>
                                    <input type="number" name="min_stock" value="10" required class="w-full rounded-lg border-slate-300">
                                    <p class="text-[10px] text-slate-400 mt-1">*Dalam satuan Pcs</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">Simpan Produk</button>
                        <button @click="openCreate = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>