<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-slate-800 mb-6">Pengajuan Retur Baru</h1>
        
        <form action="{{ route('returns.store') }}" method="POST" class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Sales Order (SO)</label>
                    <select name="sales_order_id" id="so-select" onchange="loadShippedItems()" class="w-full border-slate-300 rounded-lg text-sm" required>
                        <option value="">-- Pilih Order --</option>
                        @foreach($orders as $so)
                            <option value="{{ $so->id }}">{{ $so->so_number }} - {{ $so->customer->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-1">Hanya SO yang sudah ada pengiriman (Shipment).</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Retur</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full border-slate-300 rounded-lg text-sm" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">Alasan Pengembalian</label>
                <textarea name="reason" rows="2" class="w-full border-slate-300 rounded-lg text-sm" placeholder="Contoh: Barang cacat, Salah kirim..." required></textarea>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-700 mb-2">Barang yang Dikembalikan</label>
                
                <div id="empty-msg" class="text-sm text-slate-400 italic mb-2">Silakan pilih Sales Order terlebih dahulu untuk melihat daftar barang.</div>

                <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 space-y-3 hidden" id="product-list-container">
                    <div id="product-rows">
                        </div>
                    
                    <button type="button" onclick="addRow()" class="mt-2 text-sm text-indigo-600 font-bold hover:underline flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Baris Barang
                    </button>
                </div>
            </div>

            <div class="flex justify-end gap-4 border-t border-slate-100 pt-4">
                <a href="{{ route('returns.index') }}" class="px-4 py-2 text-slate-600 font-bold hover:bg-slate-100 rounded-lg">Batal</a>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-indigo-700">Simpan Pengajuan</button>
            </div>
        </form>
    </div>

    <script>
        let currentProducts = []; // Menyimpan data produk dari API

        // 1. Fungsi dipanggil saat SO dipilih
        function loadShippedItems() {
            const soId = document.getElementById('so-select').value;
            const container = document.getElementById('product-list-container');
            const emptyMsg = document.getElementById('empty-msg');
            const rowsContainer = document.getElementById('product-rows');

            // Reset
            rowsContainer.innerHTML = '';
            currentProducts = [];

            if (!soId) {
                container.classList.add('hidden');
                emptyMsg.classList.remove('hidden');
                return;
            }

            // Tampilkan loading (opsional)
            emptyMsg.textContent = "Sedang mengambil data barang...";

            // Fetch API
            fetch(`/api/sales/${soId}/shipped-items`)
                .then(response => response.json())
                .then(data => {
                    currentProducts = data; // Simpan data global
                    
                    if (currentProducts.length === 0) {
                        emptyMsg.textContent = "Tidak ada barang yang tercatat sudah dikirim pada Order ini.";
                        container.classList.add('hidden');
                    } else {
                        emptyMsg.classList.add('hidden');
                        container.classList.remove('hidden');
                        
                        // Tambahkan 1 baris kosong otomatis
                        addRow();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    emptyMsg.textContent = "Gagal memuat data barang.";
                });
        }

        // 2. Fungsi Menambah Baris Form
        function addRow() {
            const rowsContainer = document.getElementById('product-rows');
            
            // PENTING: Hanya tampilkan produk yang sisa kuotanya > 0
            // Property 'remaining_qty' dikirim dari Controller
            let optionsHtml = '<option value="">-- Pilih Barang --</option>';
            currentProducts.forEach(product => {
                // Tampilkan sisa kuota di dropdown
                optionsHtml += `<option value="${product.id}" data-max="${product.remaining_qty}">
                    ${product.name} (Sisa Kuota Retur: ${product.remaining_qty} ${product.unit})
                </option>`;
            });

            // Jika tidak ada produk yang bisa diretur
            if(currentProducts.length === 0) {
                 alert("Semua barang pada SO ini sudah diretur / belum ada pengiriman.");
                 return;
            }

            const rowDiv = document.createElement('div');
            rowDiv.className = 'flex gap-4 items-end product-row mb-3 pb-3 border-b border-slate-50 last:border-0';
            rowDiv.innerHTML = `
                <div class="flex-grow">
                    <label class="text-xs text-slate-500 font-bold mb-1 block">Nama Produk</label>
                    <select name="products[]" class="w-full border-slate-300 rounded-lg text-sm bg-slate-50 focus:bg-white transition-colors" onchange="validateQty(this)" required>
                        ${optionsHtml}
                    </select>
                </div>
                <div class="w-32">
                    <label class="text-xs text-slate-500 font-bold mb-1 block">Jml Retur</label>
                    <input type="number" name="quantities[]" min="1" class="w-full border-slate-300 rounded-lg text-sm" placeholder="Qty" required>
                    <div class="text-[10px] text-red-500 mt-1 hidden qty-error">Melebihi sisa</div>
                </div>
                <button type="button" onclick="removeRow(this)" class="bg-white border border-red-200 text-red-500 p-2 rounded-lg hover:bg-red-50 hover:border-red-300 transition-all mb-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            `;

            rowsContainer.appendChild(rowDiv);
        }

        // 3. Fungsi Hapus Baris
        function removeRow(btn) {
            const rows = document.querySelectorAll('.product-row');
            if (rows.length > 1) {
                btn.closest('.product-row').remove();
            } else {
                alert("Minimal harus ada satu barang.");
            }
        }

        // 4. Validasi Agar Qty Retur <= Qty Terkirim (UX Improvement)
        function validateQty(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const maxQty = selectedOption.getAttribute('data-max');
            
            const row = selectElement.closest('.product-row');
            const qtyInput = row.querySelector('input[type="number"]');
            const errorMsg = row.querySelector('.qty-error');
            
            if (maxQty) {
                qtyInput.max = maxQty;
                qtyInput.value = ''; // Reset value biar user sadar
                qtyInput.placeholder = `Max: ${maxQty}`;
                errorMsg.textContent = `Maksimal: ${maxQty}`;
                errorMsg.classList.remove('hidden');
            }
        }
    </script>
</x-app-layout>