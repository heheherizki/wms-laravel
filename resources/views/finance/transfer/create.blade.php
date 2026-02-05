<x-app-layout>
    <div class="max-w-4xl mx-auto">
        
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Transfer Saldo (Mutasi)</h1>
                <p class="text-slate-500 text-sm mt-1">Pindahkan dana antar akun/dompet internal.</p>
            </div>
            <a href="{{ route('finance.index') }}" class="text-slate-500 hover:text-slate-700 text-sm font-medium">&larr; Kembali ke Dashboard</a>
        </div>

        <form action="{{ route('finance.transfer.store') }}" method="POST">
            @csrf
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative">
                
                {{-- Decorator Arrow --}}
                <div class="hidden md:block absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-10">
                    <div class="bg-white p-2 rounded-full border border-slate-200 shadow-lg">
                        <svg class="w-8 h-8 text-indigo-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2">
                    
                    {{-- FROM ACCOUNT (SUMBER) --}}
                    <div class="p-8 bg-slate-50 border-b md:border-b-0 md:border-r border-slate-200">
                        <h3 class="text-xs font-bold text-red-600 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Sumber Dana (Keluar)
                        </h3>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Akun Asal</label>
                            <select name="from_account_id" id="from_account" class="w-full rounded-xl border-slate-300 text-sm focus:ring-red-500 focus:border-red-500 shadow-sm" required onchange="updateBalanceInfo()">
                                <option value="" data-balance="0">-- Pilih Akun --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}" data-balance="{{ $acc->balance }}">
                                        {{ $acc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="p-4 bg-white rounded-lg border border-slate-200">
                            <span class="text-xs text-slate-400 block">Saldo Tersedia:</span>
                            <span class="text-lg font-mono font-bold text-slate-700" id="balance_display">Rp 0</span>
                        </div>
                    </div>

                    {{-- TO ACCOUNT (TUJUAN) --}}
                    <div class="p-8 bg-white">
                        <h3 class="text-xs font-bold text-green-600 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Tujuan Transfer (Masuk)
                        </h3>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Pilih Akun Tujuan</label>
                            <select name="to_account_id" class="w-full rounded-xl border-slate-300 text-sm focus:ring-green-500 focus:border-green-500 shadow-sm" required>
                                <option value="">-- Pilih Akun --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">
                                        {{ $acc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Jumlah Transfer (Rp)</label>
                            <input type="number" name="amount" class="w-full rounded-xl border-slate-300 font-mono font-bold text-xl text-indigo-700 focus:ring-indigo-500 focus:border-indigo-500" placeholder="0" min="1" required>
                        </div>
                    </div>

                </div>

                {{-- FOOTER: TANGGAL & KETERANGAN --}}
                <div class="p-6 bg-slate-50 border-t border-slate-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Mutasi</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-slate-300 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Keterangan (Opsional)</label>
                            <input type="text" name="description" placeholder="Contoh: Topup Kas Kecil / Setor Tunai" class="w-full rounded-lg border-slate-300 text-sm">
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-white border-t border-slate-200 flex justify-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        Proses Transfer
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function updateBalanceInfo() {
            const select = document.getElementById('from_account');
            const balanceDisplay = document.getElementById('balance_display');
            
            // Ambil data-balance dari option yang dipilih
            const selectedOption = select.options[select.selectedIndex];
            const balance = selectedOption.getAttribute('data-balance');
            
            // Format Rupiah
            if(balance) {
                const formatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(balance);
                balanceDisplay.textContent = formatted;
                
                // Visual feedback jika saldo 0
                if(parseFloat(balance) <= 0) {
                    balanceDisplay.classList.add('text-red-600');
                    balanceDisplay.classList.remove('text-slate-700');
                } else {
                    balanceDisplay.classList.remove('text-red-600');
                    balanceDisplay.classList.add('text-slate-700');
                }
            } else {
                balanceDisplay.textContent = 'Rp 0';
            }
        }
    </script>
</x-app-layout>