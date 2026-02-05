<x-app-layout>
    <div class="max-w-5xl mx-auto">
        
        {{-- Header & Navigasi --}}
        <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-slate-500 mb-1">
                    <a href="{{ route('products.index') }}" class="hover:text-indigo-600 transition-colors">Stok Gudang</a>
                    <span>/</span>
                    <span>Riwayat</span>
                </div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Kartu Stok</h1>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 text-sm font-medium hover:bg-slate-50 transition-colors">
                    &larr; Kembali
                </a>
            </div>
        </div>

        {{-- Info Produk --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-2xl">
                        {{ substr($product->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">{{ $product->name }}</h2>
                        <p class="text-sm text-slate-500 font-mono bg-slate-100 px-2 py-0.5 rounded w-fit mt-1">{{ $product->sku }}</p>
                    </div>
                </div>
                
                <div class="flex gap-6 border-t md:border-t-0 md:border-l border-slate-100 pt-4 md:pt-0 md:pl-6">
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-bold">Stok Saat Ini</p>
                        <p class="text-2xl font-bold {{ $product->stock <= $product->min_stock ? 'text-orange-600' : 'text-slate-900' }}">
                            {{ number_format($product->stock) }} <span class="text-sm font-medium text-slate-400">{{ $product->unit }}</span>
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-bold">Lokasi Rak</p>
                        <p class="text-lg font-medium text-slate-700">{{ $product->rack_location ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Riwayat --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4 text-center">Tipe Transaksi</th>
                            <th class="px-6 py-4 text-center">Jumlah</th>
                            <th class="px-6 py-4">Referensi / Keterangan</th>
                            <th class="px-6 py-4 text-right">Oleh User</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($transactions as $log)
                        <tr class="hover:bg-slate-50 transition-colors">
                            {{-- Waktu --}}
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-700">{{ $log->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-slate-400">{{ $log->created_at->format('H:i') }} WIB</div>
                            </td>

                            {{-- Tipe --}}
                            <td class="px-6 py-4 text-center">
                                @if($log->type == 'in')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                        Barang Masuk
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                        Barang Keluar
                                    </span>
                                @endif
                            </td>

                            {{-- Jumlah --}}
                            <td class="px-6 py-4 text-center">
                                <span class="text-base font-bold {{ $log->type == 'in' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $log->type == 'in' ? '+' : '-' }}{{ number_format($log->quantity) }}
                                </span>
                            </td>

                            {{-- Referensi --}}
                            <td class="px-6 py-4">
                                @if($log->reference)
                                    <div class="text-indigo-600 font-medium text-xs bg-indigo-50 px-2 py-1 rounded w-fit mb-1 border border-indigo-100">
                                        {{ $log->reference }}
                                    </div>
                                @endif
                                <div class="text-slate-500 italic text-xs">
                                    {{ $log->notes ?: '-' }}
                                </div>
                            </td>

                            {{-- User --}}
                            <td class="px-6 py-4 text-right">
                                <div class="text-slate-700 font-medium">{{ $log->user->name ?? 'System' }}</div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-slate-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p>Belum ada riwayat transaksi untuk produk ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>