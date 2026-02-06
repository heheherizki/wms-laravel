<x-app-layout>
    {{-- PERBAIKAN: Gunakan tanda kutip SATU (') pada x-data agar tidak bentrok dengan JSON --}}
    <div x-data='{ 
        openModal: false, 
        searchSO: "", 
        salesOrders: @json($readySalesOrders), 
        get filteredOrders() {
            if (this.searchSO === "") return this.salesOrders;
            return this.salesOrders.filter(so => {
                const soNumber = so.so_number ? so.so_number.toLowerCase() : "";
                const custName = so.customer && so.customer.name ? so.customer.name.toLowerCase() : "";
                const search = this.searchSO.toLowerCase();
                return soNumber.includes(search) || custName.includes(search);
            });
        }
    }'>
        
        {{-- HEADER SECTION --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Data Pengiriman</h1>
                <p class="text-slate-500 text-sm mt-1">Surat Jalan (Delivery Order) & Status Pengiriman.</p>
            </div>
            
            <button @click="openModal = true; setTimeout(() => $refs.searchInput.focus(), 100)" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-md flex items-center gap-2 transition-all transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Pengiriman Baru
            </button>
        </div>

        {{-- FILTER BAR --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4 mb-6">
            <form method="GET" action="{{ route('shipments.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    {{-- Search Global --}}
                    <div class="lg:col-span-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No SJ, SO, atau Customer..." 
                            class="pl-10 w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    {{-- Filter Status Invoice --}}
                    <div>
                        <select name="status" class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Status Invoice</option>
                            <option value="invoiced" {{ request('status') == 'invoiced' ? 'selected' : '' }}>✅ Sudah Invoice</option>
                            <option value="uninvoiced" {{ request('status') == 'uninvoiced' ? 'selected' : '' }}>⚠️ Belum Invoice</option>
                        </select>
                    </div>

                    {{-- Filter Tanggal --}}
                    <div>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500" title="Dari Tanggal">
                    </div>

                    {{-- Tombol --}}
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors shadow-sm">Filter</button>
                        <a href="{{ route('shipments.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 text-sm font-medium">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        {{-- NOTIFIKASI SUKSES (Dengan Quick Action) --}}
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="bg-green-100 p-2 rounded-full text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-green-800">Berhasil Disimpan!</h4>
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    @if(session('new_shipment_id'))
                        <a href="{{ route('shipments.print', session('new_shipment_id')) }}" target="_blank" class="px-4 py-2 bg-white border border-green-200 text-green-700 rounded-lg text-xs font-bold hover:bg-green-50 shadow-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Cetak SJ
                        </a>
                    @endif
                    @if(session('new_invoice_id'))
                        <a href="{{ route('invoices.print', session('new_invoice_id')) }}" target="_blank" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-bold hover:bg-indigo-700 shadow-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Cetak Invoice
                        </a>
                    @endif
                </div>
            </div>
        @endif

        {{-- TABEL DATA --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden min-h-[400px]">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-6 py-4">No. Surat Jalan</th>
                        <th class="px-6 py-4">Referensi SO</th>
                        <th class="px-6 py-4">Customer</th>
                        <th class="px-6 py-4">Tanggal Kirim</th>
                        <th class="px-6 py-4 text-center">Status Invoice</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($shipments as $sj)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        
                        {{-- 1. NO SJ --}}
                        <td class="px-6 py-4">
                            <div class="font-bold text-blue-600 font-mono text-base">
                                <a href="{{ route('shipments.show', $sj->id) }}" class="hover:underline">{{ $sj->shipment_number }}</a>
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                Oleh: {{ $sj->user->name ?? 'Admin' }}
                            </div>
                        </td>

                        {{-- 2. REF SO --}}
                        <td class="px-6 py-4">
                            <a href="{{ route('sales.show', $sj->sales_order_id) }}" class="font-bold text-slate-700 hover:text-indigo-600 hover:underline">
                                {{ $sj->salesOrder->so_number }}
                            </a>
                        </td>

                        {{-- 3. CUSTOMER --}}
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-800">{{ $sj->salesOrder->customer->name }}</div>
                            @if($sj->notes)
                                <div class="text-xs text-slate-500 mt-1 italic flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                    {{ Str::limit($sj->notes, 30) }}
                                </div>
                            @endif
                        </td>

                        {{-- 4. TANGGAL --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 text-slate-600">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ date('d M Y', strtotime($sj->date)) }}
                            </div>
                        </td>

                        {{-- 5. STATUS INVOICE --}}
                        <td class="px-6 py-4 text-center">
                            @if($sj->invoice)
                                <a href="{{ route('invoices.show', $sj->invoice->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Terbit
                                </a>
                            @else
                                <form action="{{ route('invoices.createFromShipment', $sj->id) }}" method="POST" onsubmit="return confirm('Buat Invoice untuk Surat Jalan ini?');">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-colors">
                                        + Buat Invoice
                                    </button>
                                </form>
                            @endif
                        </td>

                        {{-- 6. AKSI (DROPDOWN MENU) --}}
                        <td class="px-6 py-4 text-right">
                            <div class="relative inline-block text-left" x-data="{ openDropdown: false }">
                                <button @click="openDropdown = !openDropdown" @click.away="openDropdown = false" class="p-2 rounded-lg hover:bg-slate-100 text-slate-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                                </button>

                                <div x-show="openDropdown" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-slate-100 z-20 py-1" style="display: none;">
                                    
                                    {{-- Lihat Detail --}}
                                    <a href="{{ route('shipments.show', $sj->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Lihat Detail
                                    </a>

                                    {{-- Cetak SJ --}}
                                    <a href="{{ route('shipments.print', $sj->id) }}" target="_blank" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        Cetak Surat Jalan
                                    </a>

                                    {{-- Buat / Cetak Invoice --}}
                                    @if($sj->invoice)
                                        <a href="{{ route('invoices.print', $sj->invoice->id) }}" target="_blank" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-green-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            Cetak Invoice
                                        </a>
                                    @else
                                        <form action="{{ route('invoices.createFromShipment', $sj->id) }}" method="POST" onsubmit="return confirm('Buat Invoice sekarang?');">
                                            @csrf
                                            <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                Buat Invoice
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Hapus (Hanya jika belum ada invoice) --}}
                                    @if(!$sj->invoice)
                                        <div class="border-t border-slate-100 my-1"></div>
                                        <form action="{{ route('shipments.destroy', $sj->id) }}" method="POST" onsubmit="return confirm('Hapus Surat Jalan ini? Stok akan dikembalikan.');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                <p>Belum ada pengiriman.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-slate-200 bg-slate-50">{{ $shipments->links() }}</div>
        </div>

        {{-- ================= MODAL PILIH SO (SEARCHABLE) ================= --}}
        <div x-show="openModal" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" @click="openModal = false"></div>

                <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    
                    {{-- Header Modal --}}
                    <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg leading-6 font-bold text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                Pilih Sales Order
                            </h3>
                            <p class="text-indigo-200 text-xs mt-0.5">Order dengan status Pending / Partial</p>
                        </div>
                        <button @click="openModal = false" class="text-indigo-200 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6 bg-slate-50">
                        {{-- Search Input inside Modal --}}
                        <div class="mb-4 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input x-ref="searchInput" x-model="searchSO" type="text" class="w-full pl-10 border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Ketik No SO atau Nama Customer...">
                        </div>

                        {{-- List SO --}}
                        <div class="space-y-3 max-h-[300px] overflow-y-auto pr-1">
                            <template x-if="filteredOrders.length === 0">
                                <div class="text-center py-8 text-slate-400 border-2 border-dashed border-slate-200 rounded-lg">
                                    Tidak ada Sales Order yang cocok.
                                </div>
                            </template>

                            <template x-for="so in filteredOrders" :key="so.id">
                                <a :href="'/shipments/create/' + so.id" class="block group bg-white border border-slate-200 rounded-lg p-3 hover:border-indigo-500 hover:shadow-md transition-all relative overflow-hidden">
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    <div class="flex justify-between items-center pl-2">
                                        <div>
                                            <div class="font-bold text-slate-800 text-sm group-hover:text-indigo-700" x-text="so.so_number"></div>
                                            <div class="text-xs text-slate-500 flex items-center gap-1 mt-0.5">
                                                <span x-text="so.customer.name"></span>
                                                <span>•</span>
                                                <span x-text="new Date(so.date).toLocaleDateString('id-ID')"></span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-block px-2 py-0.5 text-[10px] font-bold rounded uppercase" 
                                                  :class="so.status === 'partial' ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700'"
                                                  x-text="so.status">
                                            </span>
                                            <div class="text-xs text-indigo-600 font-bold mt-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                Proses &rarr;
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>