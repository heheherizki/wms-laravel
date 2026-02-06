<x-app-layout>
    <div class="max-w-[1920px] mx-auto space-y-6">
        
        {{-- HEADER & STATS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-1">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Data Supplier</h1>
                <p class="text-slate-500 text-sm">Kelola database vendor dan pemasok.</p>
                
                @can('create_supplier')
                <a href="{{ route('suppliers.create') }}" class="mt-4 inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-md transition-all w-full md:w-auto justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Supplier
                </a>
                @endcan
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase">Total Supplier</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['total'] }}</p>
                </div>
                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase">Supplier Tempo (Kredit)</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['credit_suppliers'] }}</p>
                </div>
                <div class="p-2 bg-purple-50 rounded-lg text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4">
            <form method="GET" action="{{ route('suppliers.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    
                    {{-- Search --}}
                    <div class="md:col-span-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama, Kode, atau Kontak..." 
                            class="pl-10 w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    {{-- Filter Termin --}}
                    <div>
                        <select name="term" class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Jenis Pembayaran</option>
                            <option value="cash" {{ request('term') == 'cash' ? 'selected' : '' }}>Cash / Tunai</option>
                            <option value="credit" {{ request('term') == 'credit' ? 'selected' : '' }}>Tempo / Kredit</option>
                        </select>
                    </div>

                    {{-- Tombol --}}
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors shadow-sm">Filter</button>
                        <a href="{{ route('suppliers.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 text-sm font-medium">Reset</a>
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
                            <th class="px-6 py-4">Informasi Supplier</th>
                            <th class="px-6 py-4">Kontak Person</th>
                            <th class="px-6 py-4">Alamat / Lokasi</th>
                            <th class="px-6 py-4 text-center">Termin Bayar</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($suppliers as $supplier)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            
                            {{-- 1. INFO UTAMA --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg shrink-0 border border-indigo-100">
                                        {{ substr($supplier->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $supplier->name }}</div>
                                        <div class="text-xs text-slate-500 font-mono bg-slate-100 px-1.5 rounded w-fit mt-0.5 border border-slate-200">
                                            {{ $supplier->code }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- 2. KONTAK --}}
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-700">{{ $supplier->contact_person ?? '-' }}</div>
                                <div class="text-xs text-slate-500 mt-1 flex flex-col gap-0.5">
                                    @if($supplier->phone)
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                            {{ $supplier->phone }}
                                        </span>
                                    @endif
                                    @if($supplier->email)
                                        <span class="flex items-center gap-1 text-indigo-600">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            {{ $supplier->email }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- 3. ALAMAT --}}
                            <td class="px-6 py-4 max-w-xs">
                                <p class="text-slate-600 text-xs leading-relaxed truncate" title="{{ $supplier->address }}">
                                    {{ $supplier->address ?? '-' }}
                                </p>
                            </td>

                            {{-- 4. TERMIN --}}
                            <td class="px-6 py-4 text-center">
                                @if($supplier->term_days > 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                        Tempo {{ $supplier->term_days }} Hari
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">
                                        Cash / Tunai
                                    </span>
                                @endif
                            </td>

                            {{-- 5. AKSI --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2 opacity-100 md:opacity-60 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('suppliers.edit', $supplier->id) }}" class="p-1.5 bg-white border border-slate-200 rounded text-slate-500 hover:text-indigo-600 hover:border-indigo-300 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    
                                    @can('delete_supplier')
                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('Hapus data supplier {{ $supplier->name }}?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-white border border-slate-200 rounded text-slate-500 hover:text-red-600 hover:border-red-300 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center bg-slate-50">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-white p-3 rounded-full shadow-sm mb-3">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <p class="font-medium text-slate-900">Tidak ada data supplier.</p>
                                    <p class="text-slate-500 text-sm mt-1">Tambahkan supplier baru untuk memulai.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- PAGINATION --}}
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $suppliers->links() }}
            </div>
        </div>
    </div>
</x-app-layout>