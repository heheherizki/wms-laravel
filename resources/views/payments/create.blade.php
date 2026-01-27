<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-slate-800 mb-6">Input Pembayaran Baru</h1>

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 mb-6 flex justify-between items-center">
                <div>
                    <span class="text-xs text-slate-500 uppercase font-bold">No. Invoice</span>
                    <div class="text-lg font-bold text-indigo-600">{{ $invoice->invoice_number }}</div>
                    <div class="text-sm text-slate-600">{{ $invoice->salesOrder->customer->name }}</div>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-500 uppercase font-bold">Sisa Tagihan</span>
                    <div class="text-2xl font-mono font-bold text-red-600">Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }}</div>
                    <div class="text-xs text-slate-400">Total Tagihan: Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</div>
                </div>
            </div>

            <form action="{{ route('payments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Bayar</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full border-slate-300 rounded-lg text-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Metode Pembayaran</label>
                        <select name="payment_method" class="w-full border-slate-300 rounded-lg text-sm" required>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="Tunai">Tunai / Cash</option>
                            <option value="Cek / Giro">Cek / Giro</option>
                            <option value="QRIS">QRIS / E-Wallet</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nominal Pembayaran (Rp)</label>
                    <input type="number" name="amount" max="{{ $invoice->remaining_balance }}" class="w-full border-slate-300 rounded-lg text-lg font-bold text-slate-800" placeholder="0" required>
                    <p class="text-xs text-slate-500 mt-1">Maksimal: {{ $invoice->remaining_balance }}</p>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Catatan / Referensi</label>
                    <textarea name="note" rows="2" class="w-full border-slate-300 rounded-lg text-sm" placeholder="Contoh: No. Referensi Transfer..."></textarea>
                </div>

                <div class="flex justify-end gap-4 border-t border-slate-100 pt-4">
                    <a href="{{ route('invoices.show', $invoice->id) }}" class="px-4 py-2 text-slate-600 font-bold hover:bg-slate-50 rounded-lg">Batal</a>
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-green-700 shadow-lg shadow-green-200">
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>