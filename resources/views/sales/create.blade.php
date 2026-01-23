<x-app-layout>
    <div class="max-w-5xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Buat Sales Order</h1>
                <p class="text-slate-500 text-sm">Input pesanan penjualan baru.</p>
            </div>
            <a href="{{ route('sales.index') }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm">
                &larr; Kembali
            </a>
        </div>

        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('sales.store') }}" method="POST" x-data="salesForm()">
            @csrf
            
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">No. SO</label>
                        <input type="text" name="so_number" value="{{ $soNumber }}" readonly class="w-full bg-slate-100 border-slate-300 rounded-lg text-sm font-mono cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full border-slate-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Customer</label>
                        <select name="customer_id" class="w-full border-slate-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Pilih Customer --</option>
                            @foreach($customers as $cust)
                                <option value="{{ $cust->id }}">{{ $cust->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan Pengiriman</label>
                    <textarea name="notes" rows="2" class="w-full border-slate-300 rounded-lg text-sm" placeholder="Contoh: Kirim via Ekspedisi JNE, Packing Kayu, dll."></textarea>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mb-6">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Item Penjualan</h3>
                
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-700 font-bold">
                        <tr>
                            <th class="px-4 py-2 w-1/2">Produk (Barang Jadi)</th>
                            <th class="px-4 py-2 w-24">Qty</th>
                            <th class="px-4 py-2 w-40">Harga Jual</th>
                            <th class="px-4 py-2 text-right">Subtotal</th>
                            <th class="px-4 py-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="px-4 py-2">
                                    <select :name="'items['+index+'][product_id]'" x-model="item.product_id" @change="updatePrice(index)" class="w-full border-slate-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Pilih Produk...</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->sell_price }}">
                                                {{ $product->name }} (Stok: {{ $product->stock }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" :name="'items['+index+'][quantity]'" x-model="item.quantity" min="1" class="w-full border-slate-300 rounded-lg text-sm text-center focus:ring-blue-500 focus:border-blue-500">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" :name="'items['+index+'][price]'" x-model="item.price" class="w-full border-slate-300 rounded-lg text-sm text-right focus:ring-blue-500 focus:border-blue-500">
                                </td>
                                <td class="px-4 py-2 text-right font-mono font-bold text-slate-700">
                                    Rp <span x-text="formatRupiah(item.quantity * item.price)"></span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="px-4 py-3">
                                <button type="button" @click="addItem()" class="text-blue-600 hover:text-blue-800 font-bold text-sm flex items-center gap-1">
                                    + Tambah Item
                                </button>
                            </td>
                        </tr>
                        <tr class="bg-blue-50 border-t border-blue-200">
                            <td colspan="3" class="px-4 py-3 text-right font-bold text-slate-700 uppercase">Grand Total</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-lg text-blue-700">
                                Rp <span x-text="formatRupiah(grandTotal())"></span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('sales.index') }}" class="px-6 py-3 border border-slate-300 rounded-xl text-slate-600 font-bold hover:bg-slate-50 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 shadow-lg hover:shadow-xl transition-all">
                    Simpan Sales Order
                </button>
            </div>
        </form>
    </div>

    <script>
        function salesForm() {
            return {
                items: [
                    { product_id: '', quantity: 1, price: 0 }
                ],
                addItem() {
                    this.items.push({ product_id: '', quantity: 1, price: 0 });
                },
                removeItem(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    } else {
                        alert('Minimal harus ada 1 item penjualan!');
                    }
                },
                updatePrice(index) {
                    // Ambil harga dari atribut data-price di option yang dipilih
                    let select = document.getElementsByName('items['+index+'][product_id]')[0];
                    let price = select.options[select.selectedIndex].getAttribute('data-price');
                    this.items[index].price = price ? price : 0;
                },
                grandTotal() {
                    return this.items.reduce((total, item) => {
                        return total + (item.quantity * item.price);
                    }, 0);
                },
                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID').format(number);
                }
            }
        }
    </script>
</x-app-layout>