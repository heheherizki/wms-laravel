<x-app-layout>
    <div class="max-w-5xl mx-auto">
        
        {{-- HEADER --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Buat Retur Pembelian</h1>
                <p class="text-slate-500 text-sm mt-1">Ajukan pengembalian barang ke Supplier.</p>
            </div>
            <a href="{{ route('purchase_returns.index') }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm flex items-center gap-1">
                &larr; Batal & Kembali
            </a>
        </div>

        <form action="{{ route('purchase_returns.store') }}" method="POST" id="returnForm">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- KOLOM KIRI: FORM HEADER --}}
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                        <h3 class="font-bold text-slate-800 mb-4 border-b pb-2">Informasi Dokumen</h3>
                        
                        {{-- PILIH PO --}}
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Pilih PO (Completed)</label>
                            <select name="purchase_id" id="purchase_id" class="w-full rounded-lg border-slate-300 text-sm focus:ring-red-500 focus:border-red-500" required onchange="loadItems()">
                                <option value="">-- Pilih Nomor PO --</option>
                                @foreach($purchases as $po)
                                    <option value="{{ $po->id }}">
                                        {{ $po->po_number }} - {{ $po->supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-slate-400 mt-1">* Hanya PO status 'Completed' (Barang diterima penuh) yang muncul.</p>
                        </div>

                        {{-- TANGGAL --}}
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Tanggal Retur</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-slate-300 text-sm">
                        </div>

                        {{-- ALASAN --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Alasan Pengembalian</label>
                            <textarea name="reason" class="w-full rounded-lg border-slate-300 text-sm" rows="4" placeholder="Jelaskan kenapa barang diretur (Misal: Cacat produksi, salah kirim tipe, expired)..." required></textarea>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: DAFTAR BARANG (AJAX) --}}
                <div class="lg:col-span-2">
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden min-h-[300px] relative">
                        
                        {{-- LOADING STATE --}}
                        <div id="loading-state" class="hidden absolute inset-0 bg-white/80 z-10 flex flex-col items-center justify-center">
                            <svg class="animate-spin h-8 w-8 text-red-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span class="text-sm font-bold text-slate-500">Memuat Barang...</span>
                        </div>

                        {{-- EMPTY STATE --}}
                        <div id="empty-state" class="absolute inset-0 flex flex-col items-center justify-center text-slate-400">
                            <svg class="w-16 h-16 mb-4 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <p class="text-sm">Silakan pilih Nomor PO di sebelah kiri untuk menampilkan barang.</p>
                        </div>

                        {{-- TABLE CONTENT --}}
                        <div id="items-container" class="hidden">
                            <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                                <h3 class="font-bold text-slate-700">Pilih Barang & Jumlah</h3>
                                <span class="text-xs text-slate-500 bg-white px-2 py-1 rounded border">Otomatis terisi dari PO</span>
                            </div>
                            <table class="w-full text-sm text-left">
                                <thead class="bg-white text-slate-500 border-b border-slate-100 uppercase text-xs">
                                    <tr>
                                        <th class="px-6 py-3">Produk</th>
                                        <th class="px-6 py-3 text-center">Qty Diterima</th>
                                        <th class="px-6 py-3 w-32 text-center bg-red-50 text-red-600">Jml Retur</th>
                                    </tr>
                                </thead>
                                <tbody id="items-table-body" class="divide-y divide-slate-50">
                                    {{-- JS akan mengisi ini --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- SUBMIT BUTTON --}}
                    <div class="mt-6 flex justify-end">
                        <button type="submit" id="submit-btn" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            Ajukan Retur Pembelian
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- JAVASCRIPT LOGIC --}}
    <script>
        function loadItems() {
            const poId = document.getElementById('purchase_id').value;
            const container = document.getElementById('items-container');
            const tbody = document.getElementById('items-table-body');
            const emptyState = document.getElementById('empty-state');
            const loadingState = document.getElementById('loading-state');
            const submitBtn = document.getElementById('submit-btn');

            if (!poId) {
                container.classList.add('hidden');
                emptyState.classList.remove('hidden');
                submitBtn.disabled = true;
                return;
            }

            // Show Loading
            emptyState.classList.add('hidden');
            loadingState.classList.remove('hidden');
            container.classList.add('hidden');

            // Fetch Data
            fetch(`/api/purchases/${poId}/items`)
                .then(response => response.json())
                .then(data => {
                    tbody.innerHTML = '';
                    loadingState.classList.add('hidden');
                    
                    if(data.length === 0) {
                        alert('Data barang tidak ditemukan atau stok PO 0.');
                        emptyState.classList.remove('hidden');
                        return;
                    }

                    container.classList.remove('hidden');
                    submitBtn.disabled = false;

                    data.forEach((item, index) => {
                        const row = `
                            <tr class="hover:bg-slate-50 group transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 text-base">${item.name}</div>
                                    <div class="text-xs text-slate-500">${item.sku}</div>
                                    <input type="hidden" name="products[]" value="${item.id}">
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg font-bold text-sm border border-slate-200">
                                        ${item.quantity_received} ${item.unit}
                                    </span>
                                </td>
                                <td class="px-6 py-4 bg-red-50/50">
                                    <input type="number" 
                                        name="quantities[]" 
                                        class="w-full text-center rounded-lg border-slate-300 text-red-600 font-bold focus:ring-red-500 focus:border-red-500" 
                                        min="0" 
                                        max="${item.quantity_received}" 
                                        value="0"
                                        oninput="highlightRow(this)"
                                    >
                                </td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadingState.classList.add('hidden');
                    alert('Gagal mengambil data barang.');
                });
        }

        // Fitur Visual: Highlight baris jika user mengisi qty > 0
        function highlightRow(input) {
            const row = input.closest('tr');
            if (input.value > 0) {
                row.classList.add('bg-red-50');
                row.classList.remove('hover:bg-slate-50');
            } else {
                row.classList.remove('bg-red-50');
                row.classList.add('hover:bg-slate-50');
            }
        }
    </script>
</x-app-layout>