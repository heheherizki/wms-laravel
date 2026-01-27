<x-app-layout>
    <div class="max-w-5xl mx-auto space-y-6">
        
        <div class="flex justify-between items-center print:hidden">
            <h1 class="text-2xl font-bold text-slate-800">Detail Invoice</h1>
            <a href="{{ route('invoices.index') }}" class="text-slate-500 hover:text-slate-800 font-medium transition-colors">
                &larr; Kembali ke Daftar
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded shadow-sm relative print:hidden">
                <span class="block sm:inline font-bold">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded shadow-sm relative print:hidden">
                <span class="block sm:inline font-bold">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
            
            <div class="p-8 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-slate-50/50">
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Invoice #</span>
                    <h2 class="text-3xl font-bold text-slate-900 font-mono mt-1">{{ $invoice->invoice_number }}</h2>
                    <div class="mt-2">
                        @if($invoice->status == 'paid')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-800 border border-green-200">
                                ✓ LUNAS (PAID)
                            </span>
                        @elseif($invoice->status == 'partial')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-orange-100 text-orange-800 border border-orange-200">
                                ⏳ BAYAR SEBAGIAN (PARTIAL)
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-800 border border-red-200">
                                ✕ BELUM DIBAYAR (UNPAID)
                            </span>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-slate-500 text-sm">Tanggal Faktur</div>
                    <div class="font-bold text-slate-800">{{ date('d F Y', strtotime($invoice->date)) }}</div>
                    <div class="text-slate-500 text-sm mt-2">Jatuh Tempo</div>
                    <div class="font-bold text-slate-800">{{ date('d F Y', strtotime($invoice->due_date)) }}</div>
                </div>
            </div>

            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Ditagihkan Kepada (Bill To)</h3>
                    <div class="font-bold text-lg text-slate-800">{{ $invoice->salesOrder->customer->name }}</div>
                    <div class="text-slate-600 mt-1 text-sm leading-relaxed">
                        {{ $invoice->salesOrder->customer->address ?? 'Alamat tidak tersedia' }}<br>
                        Telp: {{ $invoice->salesOrder->customer->phone }}
                    </div>
                </div>
                <div class="md:text-right">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Referensi Order</h3>
                    <div class="space-y-1">
                        <div class="text-sm">
                            <span class="text-slate-500">Sales Order:</span> 
                            <span class="font-mono font-bold text-indigo-600">{{ $invoice->salesOrder->so_number }}</span>
                        </div>
                        <div class="text-sm">
                            <span class="text-slate-500">Salesperson:</span> 
                            <span class="font-medium">{{ $invoice->salesOrder->user->name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-8 pb-8">
                <div class="border rounded-lg overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-100 text-slate-600 font-bold border-b">
                            <tr>
                                <th class="px-4 py-3 text-left">Deskripsi Produk</th>
                                <th class="px-4 py-3 text-center">Qty</th>
                                <th class="px-4 py-3 text-right">Harga Satuan</th>
                                <th class="px-4 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($invoice->details as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-800">{{ $item->product->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $item->product->sku }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-bold text-slate-800">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="px-8 pb-8 flex flex-col md:flex-row justify-end">
                <div class="w-full md:w-1/3 space-y-3">
                    <div class="flex justify-between text-sm text-slate-600">
                        <span>Subtotal</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t border-slate-200 my-2"></div>
                    
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-slate-800">Total Tagihan</span>
                        <span class="text-xl font-bold text-slate-900">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between items-center text-sm text-green-600">
                        <span>Sudah Dibayar (-)</span>
                        <span class="font-bold">Rp {{ number_format($invoice->total_paid, 0, ',', '.') }}</span>
                    </div>

                    <div class="bg-slate-100 p-3 rounded-lg flex justify-between items-center border border-slate-200">
                        <span class="font-bold text-red-600 uppercase text-xs">Sisa Tagihan (Balance)</span>
                        <span class="font-mono font-bold text-lg text-red-600">Rp {{ number_format($invoice->remaining_balance, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 border-t border-slate-200 p-8">
                <div class="flex justify-between items-end mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Riwayat Pembayaran</h3>
                        <p class="text-sm text-slate-500">Daftar pembayaran yang diterima untuk faktur ini.</p>
                    </div>
                    @if($invoice->status != 'paid')
                        <a href="{{ route('payments.create', $invoice->id) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-md flex items-center gap-2 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Input Pembayaran
                        </a>
                    @endif
                </div>

                <div class="bg-white border border-slate-200 rounded-lg shadow-sm overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-100 text-slate-600 font-bold border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3">No. Kwitansi</th>
                                <th class="px-6 py-3">Tanggal</th>
                                <th class="px-6 py-3">Metode</th>
                                <th class="px-6 py-3 text-right">Nominal</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($invoice->payments as $pay)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3 font-mono font-bold text-indigo-600">{{ $pay->payment_number }}</td>
                                <td class="px-6 py-3">{{ date('d/m/Y', strtotime($pay->date)) }}</td>
                                <td class="px-6 py-3">
                                    <div class="font-medium text-slate-800">{{ $pay->payment_method }}</div>
                                    @if($pay->note) <div class="text-xs text-slate-400 italic truncate max-w-[200px]">{{ $pay->note }}</div> @endif
                                </td>
                                <td class="px-6 py-3 text-right font-bold text-green-600">
                                    Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3 text-center flex justify-center gap-2">
                                    <a href="{{ route('payments.print', $pay->id) }}" target="_blank" class="text-slate-500 hover:text-blue-600 border border-slate-200 hover:border-blue-300 bg-white px-2 py-1 rounded transition-colors" title="Cetak Kwitansi">
                                        🖨️
                                    </a>
                                    
                                    @if(Auth::user()->role === 'admin')
                                    <form action="{{ route('payments.destroy', $pay->id) }}" method="POST" onsubmit="return confirm('Hapus pembayaran ini? Status Invoice akan dihitung ulang.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 border border-slate-200 hover:border-red-300 bg-white px-2 py-1 rounded transition-colors" title="Hapus Data">
                                            🗑️
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-400 italic">
                                    Belum ada data pembayaran.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-4 bg-slate-100 border-t border-slate-200 flex justify-end gap-3 print:hidden">
                <a href="{{ route('invoices.print', $invoice->id) }}" target="_blank" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg font-bold text-sm flex items-center gap-2 shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Faktur (PDF)
                </a>
            </div>

        </div>
    </div>
</x-app-layout>