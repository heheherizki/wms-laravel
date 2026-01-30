<x-app-layout>
    <div class="max-w-xl mx-auto">
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-lg text-slate-800">Pembayaran Hutang Supplier</h3>
                <p class="text-sm text-slate-500 mt-1">
                    PO: <span class="font-mono font-bold text-slate-700">{{ $purchase->po_number }}</span> 
                    | {{ $purchase->supplier->name }}
                </p>
            </div>
            
            <form action="{{ route('purchases.payment.store') }}" method="POST" class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">

                {{-- Info Tagihan (Card) --}}
                <div class="bg-indigo-50 p-5 rounded-xl border border-indigo-100 shadow-inner">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-slate-600 font-medium">Total Tagihan</span>
                        {{-- PERBAIKAN: Gunakan total_amount --}}
                        <span class="font-bold text-slate-800">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-3">
                        <span class="text-slate-600 font-medium">Sudah Dibayar</span>
                        <span class="font-bold text-green-600">
                            - Rp {{ number_format($purchase->amount_paid, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="border-t border-indigo-200 pt-3 flex justify-between items-center">
                        <span class="text-indigo-900 font-bold">Sisa Hutang</span>
                        <span class="text-xl font-bold text-indigo-700 font-mono">
                            {{-- PERBAIKAN: Gunakan total_amount untuk hitung sisa --}}
                            Rp {{ number_format($purchase->total_amount - $purchase->amount_paid, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Tanggal Bayar</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Metode Bayar</label>
                        <select name="payment_method" class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="Cash">Tunai (Cash)</option>
                            <option value="Giro">Giro / Cek</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Nominal Pembayaran</label>
                    <div class="relative">
                        <span class="absolute left-4 top-2.5 text-slate-500 font-bold text-sm">Rp</span>
                        {{-- PERBAIKAN: Gunakan total_amount untuk validasi max input --}}
                        <input type="number" name="amount" 
                               class="w-full rounded-lg border-slate-300 pl-12 py-2.5 font-bold text-slate-800 focus:ring-indigo-500 focus:border-indigo-500 text-lg" 
                               placeholder="0" 
                               min="1"
                               max="{{ $purchase->total_amount - $purchase->amount_paid }}" 
                               required>
                    </div>
                    <p class="text-xs text-slate-400 mt-2">Maksimal pembayaran sesuai sisa hutang.</p>
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                    <a href="{{ route('purchases.index') }}" class="px-5 py-2.5 border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 font-bold text-sm transition-colors">Batal</a>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold text-sm shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Bayar Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>