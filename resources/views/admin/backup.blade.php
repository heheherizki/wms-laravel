<x-app-layout>
    <div class="max-w-6xl mx-auto space-y-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">System Maintenance</h1>
                <p class="text-slate-500 mt-1">Pusat kontrol backup dan pemulihan data aplikasi.</p>
            </div>
        </div>

        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Disarankan untuk melakukan <strong>Download Backup</strong> secara rutin (misal: Mingguan) dan menyimpannya di tempat aman (Google Drive / Hardisk Eksternal).
                    </p>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Berhasil!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Gagal!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 bg-slate-50 border-b border-slate-100">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">Backup Database</h3>
                    <p class="text-slate-500 text-sm mt-2">Download seluruh data aplikasi (Produk, Transaksi, User, dll) ke dalam satu file SQL.</p>
                </div>
                <div class="p-6">
                    <a href="{{ route('system.backup.download') }}" class="w-full flex justify-center items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white font-bold py-3 px-4 rounded-xl transition-all shadow-lg shadow-slate-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download Full Backup (.sql)
                    </a>
                    <p class="text-center text-xs text-slate-400 mt-4">File yang didownload bisa digunakan untuk restore di kolom sebelah kanan.</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-red-100 overflow-hidden">
                <div class="p-6 bg-red-50 border-b border-red-100">
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-red-700">Restore Database</h3>
                    <p class="text-red-600/70 text-sm mt-2">
                        ⚠️ <strong>Peringatan:</strong> Tindakan ini akan menghapus seluruh data saat ini dan menggantinya dengan data dari file backup.
                    </p>
                </div>
                <div class="p-6">
                    <form action="{{ route('system.backup.restore') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('APAKAH ANDA YAKIN? Data saat ini akan TIMPA dengan data backup. Tindakan ini tidak bisa dibatalkan!');">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Pilih File Backup (.sql)</label>
                            <input type="file" name="backup_file" required accept=".sql" 
                                class="block w-full text-sm text-slate-500
                                file:mr-4 file:py-2.5 file:px-4
                                file:rounded-lg file:border-0
                                file:text-sm file:font-semibold
                                file:bg-red-50 file:text-red-700
                                hover:file:bg-red-100 border border-slate-300 rounded-lg">
                        </div>

                        <button type="submit" class="w-full flex justify-center items-center gap-2 bg-white border-2 border-red-600 text-red-600 hover:bg-red-600 hover:text-white font-bold py-3 px-4 rounded-xl transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Mulai Proses Restore
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <div class="mt-8">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Backup Parsial (Excel)</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('products.index') }}" class="flex items-center p-4 bg-white border border-slate-200 rounded-xl hover:shadow-md transition-all">
                    <div class="bg-indigo-50 p-2 rounded-lg text-indigo-600 mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <div>
                        <div class="font-bold text-slate-800">Master Produk</div>
                        <div class="text-xs text-slate-500">Import/Export via Menu Produk</div>
                    </div>
                </a>
                </div>
        </div>

    </div>
</x-app-layout>