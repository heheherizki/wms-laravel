<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        {{-- 1. HEADER & ACTION --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Dashboard Keuangan</h1>
                <p class="text-sm text-slate-500">Ringkasan posisi kas dan aset lancar.</p>
            </div>
            
            <div class="flex gap-2">
                {{-- Tombol Riwayat --}}
                <a href="{{ route('finance.transactions.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Riwayat
                </a>

                {{-- Tombol Transfer (BARU) --}}
                <a href="{{ route('finance.transfer.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-50 border border-indigo-200 text-indigo-700 text-sm font-medium rounded-lg hover:bg-indigo-100 transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    Mutasi
                </a>

                {{-- Tombol Tambah Akun --}}
                <a href="{{ route('finance.accounts.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-900 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Akun
                </a>
            </div>
        </div>

        {{-- FLASH MESSAGES --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-white border-l-4 border-green-500 rounded-lg shadow-sm flex items-center gap-3">
                <div class="text-green-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-sm font-medium text-slate-700">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-white border-l-4 border-red-500 rounded-lg shadow-sm flex items-center gap-3">
                <div class="text-red-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <p class="text-sm font-medium text-slate-700">{{ session('error') }}</p>
            </div>
        @endif

        {{-- 2. SUMMARY CARDS (STATS) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            {{-- Total Saldo --}}
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
                <dt class="text-sm font-medium text-slate-500 truncate">Total Likuiditas (Cash)</dt>
                <dd class="mt-2 text-3xl font-bold text-slate-900">Rp {{ number_format($totalCash, 0, ',', '.') }}</dd>
                <div class="absolute top-0 right-0 p-4 opacity-5">
                    <svg class="w-24 h-24 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>

            {{-- Akun Aktif --}}
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <dt class="text-sm font-medium text-slate-500 truncate">Dompet & Rekening Aktif</dt>
                <dd class="mt-2 text-3xl font-bold text-slate-900">{{ $accounts->count() }} <span class="text-sm font-normal text-slate-400">Akun</span></dd>
            </div>

            {{-- Kategori Biaya --}}
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <dt class="text-sm font-medium text-slate-500 truncate">Pos Biaya Operasional</dt>
                <dd class="mt-2 text-3xl font-bold text-slate-900">{{ $categories->count() }} <span class="text-sm font-normal text-slate-400">Kategori</span></dd>
            </div>
        </div>

        {{-- 3. MAIN CONTENT GRID --}}
        <div class="grid grid-cols-12 gap-8 items-start">
            
            {{-- KOLOM KIRI: DAFTAR AKUN (8/12) --}}
            <div class="col-span-12 lg:col-span-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-800">Daftar Akun</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($accounts as $acc)
                        @php
                            $isBank = preg_match('/(Bank|BCA|Mandiri|BRI|BNI|BSI)/i', $acc->name);
                        @endphp
                        
                        <div class="group bg-white rounded-lg border border-slate-200 p-5 hover:border-indigo-500 transition-colors shadow-sm relative">
                            
                            {{-- Header Card --}}
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-3">
                                    {{-- Icon Box --}}
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $isBank ? 'bg-blue-50 text-blue-600' : 'bg-emerald-50 text-emerald-600' }}">
                                        @if($isBank)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-sm">{{ $acc->name }}</h4>
                                        <p class="text-xs text-slate-400 font-mono">{{ $acc->account_number ?? 'Tunai' }}</p>
                                    </div>
                                </div>

                                {{-- Dropdown Action --}}
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="text-slate-300 hover:text-slate-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-32 bg-white rounded shadow-lg border border-slate-100 z-10" style="display: none;">
                                        <a href="{{ route('finance.accounts.edit', $acc->id) }}" class="block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50">Edit</a>
                                        @if($acc->transactions_count == 0)
                                            <form action="{{ route('finance.accounts.destroy', $acc->id) }}" method="POST" onsubmit="return confirm('Hapus?');">
                                                @csrf @method('DELETE')
                                                <button class="block w-full text-left px-4 py-2 text-xs text-red-600 hover:bg-red-50">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Balance --}}
                            <div>
                                <p class="text-[10px] uppercase font-bold text-slate-400">Saldo Tersedia</p>
                                <p class="text-xl font-mono font-bold text-slate-900 mt-1">Rp {{ number_format($acc->balance, 0, ',', '.') }}</p>
                            </div>

                        </div>
                    @empty
                        <div class="col-span-full bg-slate-50 rounded-lg border-2 border-dashed border-slate-300 p-8 text-center">
                            <p class="text-slate-500 text-sm mb-3">Belum ada akun terdaftar.</p>
                            <a href="{{ route('finance.accounts.create') }}" class="text-indigo-600 font-bold text-sm hover:underline">Tambah Akun Sekarang</a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- KOLOM KANAN: KATEGORI BIAYA (4/12) --}}
            <div class="col-span-12 lg:col-span-4">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm sticky top-6">
                    <div class="p-4 border-b border-slate-100 bg-slate-50 rounded-t-xl flex justify-between items-center">
                        <h3 class="font-bold text-slate-800">Kategori Biaya</h3>
                        <span class="text-xs bg-slate-200 text-slate-600 px-2 py-0.5 rounded-full">{{ $categories->count() }}</span>
                    </div>

                    <div class="p-4">
                        {{-- Form Add Category --}}
                        <form action="{{ route('finance.categories.store') }}" method="POST" class="flex gap-2 mb-4">
                            @csrf
                            <input type="text" name="name" placeholder="Tambah kategori..." class="flex-1 text-sm rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500" required>
                            <button type="submit" class="bg-indigo-600 text-white p-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            </button>
                        </form>

                        {{-- List --}}
                        <div class="space-y-1 max-h-[400px] overflow-y-auto">
                            @foreach($categories as $cat)
                                <div class="flex justify-between items-center group p-2 hover:bg-slate-50 rounded-lg transition-colors">
                                    <span class="text-sm text-slate-700">{{ $cat->name }}</span>
                                    <form action="{{ route('finance.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Hapus kategori?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-slate-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            @endforeach

                            @if($categories->isEmpty())
                                <p class="text-center text-xs text-slate-400 italic py-4">Belum ada kategori.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>