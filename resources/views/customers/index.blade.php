<x-app-layout>
    <div class="max-w-7xl mx-auto">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Data Customer</h1>
                <p class="text-slate-500 mt-1">Kelola data pelanggan, kontak, dan profil risiko keuangan.</p>
            </div>
            <a href="{{ route('customers.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-sm flex items-center gap-2 transition-all transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Customer
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded shadow-sm relative flex items-start gap-3" role="alert">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="block sm:inline font-medium">{{ session('success') }}</span>
            </div>
        @endif
        
        @if($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded shadow-sm relative text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 uppercase tracking-wider text-xs">
                        <tr>
                            <th class="px-6 py-4">Kode</th>
                            <th class="px-6 py-4">Identitas Customer</th>
                            <th class="px-6 py-4">Kontak</th>
                            <th class="px-6 py-4">Termin</th>
                            <th class="px-6 py-4 text-right">Credit Limit</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($customers as $cust)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4 font-mono font-bold text-indigo-600 bg-slate-50/50 group-hover:bg-slate-50">
                                {{ $cust->code }}
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 text-base">{{ $cust->name }}</div>
                                <div class="text-xs text-slate-500 mt-1 flex items-start gap-1 max-w-[250px]">
                                    <svg class="w-3 h-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="truncate">{{ $cust->address ?? '-' }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2 text-slate-700">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        {{ $cust->phone ?? '-' }}
                                    </div>
                                    <div class="flex items-center gap-2 text-slate-500 text-xs">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        {{ $cust->email ?? '-' }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $cust->payment_terms ?? 'Cash' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                @if($cust->credit_limit > 0)
                                    <div class="font-mono font-bold text-slate-700">Rp {{ number_format($cust->credit_limit, 0, ',', '.') }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Batas Kredit</div>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Unlimited
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    
                                    @php
                                        // 1. Cek Status Unlock (Apakah sedang masa bebas?)
                                        $isUnlocked = $cust->authorized_until && now()->lt($cust->authorized_until);
                                        
                                        // 2. HITUNG TOTAL EKSPOSUR REAL (Fix Logic)
                                        // Hutang Invoice + Sales Order yang masih gantung (Pending/Hold)
                                        $pendingExposure = $cust->salesOrders()
                                                            ->whereIn('status', ['pending', 'on_hold'])
                                                            ->where('payment_status', '!=', 'paid')
                                                            ->sum('grand_total');

                                        $totalExposure = $cust->current_debt + $pendingExposure;

                                        // 3. Cek Kesehatan
                                        // Limit Jebol?
                                        $isOverLimit = $cust->credit_limit > 0 && ($totalExposure > $cust->credit_limit);
                                        // Invoice Macet?
                                        $isOverdue   = $cust->hasOverdueInvoices();
                                        
                                        $isTrouble   = $isOverLimit || $isOverdue;
                                    @endphp

                                    {{-- LOGIKA TOMBOL UNLOCK --}}
                                    
                                    @if($isUnlocked)
                                        {{-- KONDISI 1: SEDANG DI-UNLOCK (Tampilkan tombol Perpanjang) --}}
                                        <form action="{{ route('customers.unlock', $cust->id) }}" method="POST" onsubmit="return confirm('Perpanjang masa unlock 1 JAM lagi?');">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 transition-colors relative group" title="Sedang Aktif (Klik untuk Perpanjang)">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                                                <span class="absolute -top-1 -right-1 flex h-2.5 w-2.5">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                                                </span>
                                            </button>
                                        </form>

                                    @elseif($isTrouble)
                                        {{-- KONDISI 2: BERMASALAH (Kena Limit / Overdue) -> Tampilkan Tombol Gembok Merah --}}
                                        <form action="{{ route('customers.unlock', $cust->id) }}" method="POST" onsubmit="return confirm('Customer ini bermasalah (Limit/Overdue). \n\nBuka blokir kredit selama 1 JAM?');">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="p-2 bg-red-50 text-red-500 hover:text-red-700 hover:bg-red-100 rounded-lg transition-colors border border-red-100 group relative" title="TERKUNCI: Klik untuk Buka Blokir">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                
                                                {{-- Tooltip Info Kenapa Merah --}}
                                                @if($isOverLimit)
                                                    <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-[10px] font-mono text-white bg-red-600 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-10">
                                                        Over: {{ number_format($totalExposure, 0,',','.') }} / {{ number_format($cust->credit_limit, 0,',','.') }}
                                                    </span>
                                                @endif
                                            </button>
                                        </form>

                                    @else
                                        {{-- KONDISI 3: SEHAT (Aman) -> Tampilkan Badge Centang --}}
                                        <div class="p-2 text-emerald-500 bg-emerald-50 rounded-lg border border-emerald-100 cursor-default" title="Status Kredit Aman">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                    @endif

                                    <a href="{{ route('customers.statement', $cust->id) }}" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat Kartu Piutang">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </a>

                                    {{-- TOMBOL STANDAR (Edit & Delete) --}}
                                    <a href="{{ route('customers.edit', $cust->id) }}" class="p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit Data">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    
                                    <form action="{{ route('customers.destroy', $cust->id) }}" method="POST" onsubmit="return confirm('Hapus customer ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Customer">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 bg-slate-50">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <p class="font-medium">Belum ada data customer.</p>
                                    <p class="text-sm mt-1">Silakan tambahkan customer baru untuk memulai transaksi.</p>
                                    <a href="{{ route('customers.create') }}" class="mt-4 text-indigo-600 hover:underline font-bold text-sm">Tambah Customer Sekarang &rarr;</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>