<x-app-layout>
    <div class="max-w-[1920px] mx-auto space-y-6">
        
        {{-- HEADER & STATS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Judul --}}
            <div class="md:col-span-1">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Manajemen User</h1>
                <p class="text-slate-500 text-sm">Kelola akses staff dan administrator sistem.</p>
                
                <a href="{{ route('users.create') }}" class="mt-4 inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-md transition-all w-full md:w-auto justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Tambah User
                </a>
            </div>

            {{-- Kartu Statistik --}}
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase">Total User Aktif</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['total'] }}</p>
                </div>
                <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-500 font-bold uppercase">Level Admin / Manager</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['admins'] }}</p>
                </div>
                <div class="p-2 bg-purple-50 rounded-lg text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4">
            <form method="GET" action="{{ route('users.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    
                    {{-- Search --}}
                    <div class="md:col-span-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau Email..." 
                            class="pl-10 w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    {{-- Filter Role --}}
                    <div>
                        <select name="role" class="w-full rounded-lg border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Semua Role / Jabatan</option>
                            @foreach($roles as $roleName)
                                <option value="{{ $roleName }}" {{ request('role') == $roleName ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $roleName)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tombol --}}
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors shadow-sm">Filter</button>
                        <a href="{{ route('users.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 text-sm font-medium">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        {{-- FLASH MESSAGE --}}
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="bg-green-50 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded shadow-sm relative flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        {{-- TABEL DATA --}}
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Informasi User</th>
                            <th class="px-6 py-4">Kontak</th>
                            <th class="px-6 py-4 text-center">Role / Jabatan</th>
                            <th class="px-6 py-4 text-center">Terdaftar Sejak</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            
                            {{-- 1. INFO USER --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm border border-indigo-100 uppercase">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $user->name }}</div>
                                        <div class="text-xs text-slate-400">ID: #{{ $user->id }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- 2. EMAIL --}}
                            <td class="px-6 py-4">
                                <div class="text-slate-600 font-medium flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    {{ $user->email }}
                                </div>
                            </td>

                            {{-- 3. ROLE --}}
                            <td class="px-6 py-4 text-center">
                                @foreach($user->getRoleNames() as $role)
                                    @php
                                        $badgeColor = 'bg-slate-100 text-slate-600 border-slate-200';
                                        if($role == 'super_admin') $badgeColor = 'bg-purple-100 text-purple-700 border-purple-200';
                                        elseif($role == 'admin') $badgeColor = 'bg-indigo-100 text-indigo-700 border-indigo-200';
                                        elseif($role == 'manager') $badgeColor = 'bg-blue-100 text-blue-700 border-blue-200';
                                        elseif($role == 'staff') $badgeColor = 'bg-emerald-100 text-emerald-700 border-emerald-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $badgeColor }}">
                                        {{ ucwords(str_replace('_', ' ', $role)) }}
                                    </span>
                                @endforeach
                                @if($user->getRoleNames()->isEmpty())
                                    <span class="text-xs text-slate-400 italic">No Role</span>
                                @endif
                            </td>

                            {{-- 4. TANGGAL --}}
                            <td class="px-6 py-4 text-center text-slate-500 text-xs">
                                {{ $user->created_at->format('d M Y') }}
                            </td>

                            {{-- 5. AKSI --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2 opacity-100 md:opacity-60 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('users.edit', $user->id) }}" class="p-1.5 bg-white border border-slate-200 rounded text-slate-500 hover:text-indigo-600 hover:border-indigo-300 transition-colors" title="Edit User">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </a>
                                    
                                    @if(auth()->id() != $user->id)
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user {{ $user->name }}? Akses login akan hilang permanen.');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-white border border-slate-200 rounded text-slate-500 hover:text-red-600 hover:border-red-300 transition-colors" title="Hapus User">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center bg-slate-50">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-white p-3 rounded-full shadow-sm mb-3">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    </div>
                                    <p class="font-medium text-slate-900">Tidak ada user ditemukan.</p>
                                    <p class="text-slate-500 text-sm mt-1">Coba ubah filter pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- PAGINATION --}}
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>