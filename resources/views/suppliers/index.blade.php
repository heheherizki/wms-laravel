<x-app-layout>
    <div class="max-w-7xl mx-auto">
        <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Data Supplier</h1>
                <p class="text-slate-500 mt-1">Kelola data vendor dan pemasok barang/sparepart.</p>
            </div>
            {{-- Tombol Mengarah ke Halaman Create --}}
            <a href="{{ route('suppliers.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold shadow-sm flex items-center gap-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Supplier
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded shadow-sm relative font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4">Kode</th>
                            <th class="px-6 py-4">Nama Supplier</th>
                            <th class="px-6 py-4">Kontak (Sales)</th>
                            <th class="px-6 py-4">Info Kontak</th>
                            <th class="px-6 py-4">Termin Pembayaran</th> {{-- INTEGRASI KEUANGAN --}}
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($suppliers as $supplier)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs font-bold text-slate-500">{{ $supplier->code }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">{{ $supplier->name }}</div>
                                <div class="text-xs text-slate-500 truncate max-w-[200px]">{{ $supplier->address }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $supplier->contact_person ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs">
                                <div class="font-medium text-slate-700">{{ $supplier->phone ?? '-' }}</div>
                                <div class="text-slate-400">{{ $supplier->email ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($supplier->term_days > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                        Tempo: {{ $supplier->term_days }} Hari
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        Cash / Tunai
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('suppliers.edit', $supplier->id) }}" class="text-slate-400 hover:text-indigo-600 transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    
                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('Hapus supplier {{ $supplier->name }}?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-600 transition-colors" title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 bg-slate-50">
                                <p>Belum ada data supplier.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>