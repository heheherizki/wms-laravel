<x-app-layout>
    <div class="max-w-[1920px] mx-auto space-y-6">
        
        {{-- HEADER & STATS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Judul --}}
            <div class="md:col-span-1">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Data Pelanggan</h1>
                <p class="text-slate-500 text-sm">Database customer & profil kredit.</p>
                
                @can('create_customer')
                <a href="{{ route('customers.create') }}" class="mt-4 inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-md transition-all w-full md:w-auto justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Customer
                </a>
                @endcan
            </div>

            {{-- Kartu Statistik --}}
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase">Total Pelanggan</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['total'] }}</p>
                </div>
                <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase">Over Limit (Bermasalah)</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['over_limit_count'] }}</p>
                </div>
                <div class="p-2 bg-red-50 rounded-lg text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4">
            <form method="GET" action="{{ route('customers.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    
                    {{-- Search --}}
                    <div class="md:col-span-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, Kode, atau Kontak Person..." 
                            class="pl-10 w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    {{-- Tombol --}}
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors shadow-sm">Filter</button>
                        <a href="{{ route('customers.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 text-sm font-medium">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABEL DATA --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Kode</th>
                            <th class="px-6 py-4">Identitas Customer</th>
                            <th class="px-6 py-4">Kontak</th>
                            <th class="px-6 py-4 text-center">Termin</th>
                            <th class="px-6 py-4 text-right">Limit Kredit</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($customers as $cust)
                        @php
                            // Cek Limit Jebol
                            $isOverLimit = $cust->credit_limit > 0 && $cust->current_debt > $cust->credit_limit;
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors group {{ $isOverLimit ? 'bg-red-50 hover:bg-red-100' : '' }}">
                            
                            {{-- 1. KODE --}}
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded text-xs">
                                    {{ $cust->code }}
                                </span>
                            </td>

                            {{-- 2. IDENTITAS --}}
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 text-base {{ $isOverLimit ? 'text-red-700' : '' }}">{{ $cust->name }}</div>
                                <div class="text-xs text-slate-500 mt-1 flex items-start gap-1 max-w-[250px]">
                                    <svg class="w-3 h-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="truncate">{{ $cust->address ?? '-' }}</span>
                                </div>
                                @if($isOverLimit)
                                    <span class="mt-1 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-200 text-red-800">
                                        OVER LIMIT
                                    </span>
                                @endif
                            </td>

                            {{-- 3. KONTAK --}}
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-700">{{ $cust->contact_person ?? '-' }}</div>
                                <div class="text-xs text-slate-500 mt-1 flex flex-col gap-0.5">
                                    @if($cust->phone)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                            {{ $cust->phone }}
                                        </span>
                                    @endif
                                    @if($cust->email)
                                        <span class="flex items-center gap-1 text-indigo-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            {{ $cust->email }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- 4. TERMIN --}}
                            <td class="px-6 py-4 text-center">
                                @if($cust->payment_terms > 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                        Tempo {{ $cust->payment_terms }} Hari
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">
                                        Cash / Tunai
                                    </span>
                                @endif
                            </td>

                            {{-- 5. LIMIT --}}
                            <td class="px-6 py-4 text-right">
                                @if($cust->credit_limit > 0)
                                    <div class="font-mono font-bold text-slate-700">Rp {{ number_format($cust->credit_limit, 0, ',', '.') }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        Pakai: Rp {{ number_format($cust->current_debt, 0, ',', '.') }}
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-100">
                                        Unlimited
                                    </span>
                                @endif
                            </td>

                            {{-- 6. AKSI --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Edit --}}
                                    <a href="{{ route('customers.edit', $cust->id) }}" class="p-2 bg-white border border-slate-200 text-slate-500 rounded-lg hover:text-indigo-600 hover:border-indigo-300 transition-colors" title="Edit Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    
                                    {{-- Hapus --}}
                                    @can('delete_customer')
                                    <form action="{{ route('customers.destroy', $cust->id) }}" method="POST" onsubmit="return confirm('Hapus customer {{ $cust->name }}?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 bg-white border border-slate-200 text-slate-500 rounded-lg hover:text-red-600 hover:border-red-300 transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 bg-slate-50">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-white p-3 rounded-full shadow-sm mb-3">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <p class="font-medium text-slate-900">Belum ada data customer.</p>
                                    <p class="text-slate-500 text-sm mt-1">Tambahkan customer baru untuk memulai.</p>
                                    <a href="{{ route('customers.create') }}" class="mt-4 text-indigo-600 hover:underline font-bold text-sm">Tambah Customer &rarr;</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- PAGINATION --}}
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
</x-app-layout>