<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'WMS Gudang') }}</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #475569; border-radius: 4px; }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased" 
      x-data="{ 
          mobileMenuOpen: false, 
          isExpanded: localStorage.getItem('sidebarExpanded') === 'true' 
      }" 
      x-init="$watch('isExpanded', val => localStorage.setItem('sidebarExpanded', val))">

    <div x-show="mobileMenuOpen" x-cloak
         x-transition.opacity
         @click="mobileMenuOpen = false"
         class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden backdrop-blur-sm">
    </div>

    <aside class="fixed inset-y-0 left-0 z-50 bg-slate-900 text-white transition-all duration-300 ease-in-out flex flex-col shadow-2xl"
           :class="[
               mobileMenuOpen ? 'translate-x-0 w-64' : '-translate-x-full lg:translate-x-0',
               isExpanded ? 'lg:w-64' : 'lg:w-20'
           ]">
        
        <div class="h-16 flex items-center shrink-0 bg-slate-950 sticky top-0 z-10"
             :class="isExpanded ? 'px-6' : 'justify-center px-0'">
            
            <div class="flex items-center gap-3 overflow-hidden whitespace-nowrap">
                <div class="bg-indigo-600 p-1.5 rounded-lg shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <span x-show="isExpanded" x-cloak class="font-bold text-xl tracking-wider">
                    WMS<span class="text-indigo-500">Pro</span>
                </span>
            </div>
        </div>

        <div class="flex-1 py-4 flex flex-col gap-1 overflow-y-auto overflow-x-hidden"
             :class="[
                 isExpanded ? 'px-3 sidebar-scroll' : 'px-2 items-center no-scrollbar'
             ]">
            
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-3 py-2.5 rounded-lg transition-colors group relative shrink-0
               {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}"
               :class="isExpanded ? 'px-3' : 'w-10 justify-center px-0'">
                
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Dashboard</span>

                <div x-show="!isExpanded" x-cloak class="fixed left-16 ml-2 bg-slate-900 text-white text-xs px-2 py-1.5 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[60] pointer-events-none border border-slate-700">
                    Dashboard
                </div>
            </a>

            <div x-show="isExpanded" x-cloak class="mt-6 mb-2 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider shrink-0">
                Operasional
            </div>
            <div x-show="!isExpanded" x-cloak class="my-2 border-t border-slate-700 w-6 shrink-0"></div>

            <a href="{{ route('products.index') }}" 
               class="flex items-center gap-3 py-2.5 rounded-lg transition-colors group relative shrink-0
               {{ request()->routeIs('products.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}"
               :class="isExpanded ? 'px-3' : 'w-10 justify-center px-0'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Stok Gudang</span>
                <div x-show="!isExpanded" x-cloak class="fixed left-16 ml-2 bg-slate-900 text-white text-xs px-2 py-1.5 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[60] pointer-events-none border border-slate-700">Stok Gudang</div>
            </a>

            <a href="{{ route('purchases.index') }}" 
               class="flex items-center gap-3 py-2.5 rounded-lg transition-colors group relative shrink-0
               {{ request()->routeIs('purchases.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}"
               :class="isExpanded ? 'px-3' : 'w-10 justify-center px-0'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Pembelian (PO)</span>
                <div x-show="!isExpanded" x-cloak class="fixed left-16 ml-2 bg-slate-900 text-white text-xs px-2 py-1.5 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[60] pointer-events-none border border-slate-700">Pembelian</div>
            </a>

            <a href="{{ route('purchase_returns.index') }}" 
               class="flex items-center gap-3 py-2.5 rounded-lg transition-colors group relative shrink-0
               {{ request()->routeIs('purchase_returns.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}"
               :class="isExpanded ? 'px-3' : 'w-10 justify-center px-0'">
                
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Retur Pembelian</span>
                
                <div x-show="!isExpanded" x-cloak class="fixed left-16 ml-2 bg-slate-900 text-white text-xs px-2 py-1.5 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[60] pointer-events-none border border-slate-700">Retur Pembelian</div>
            </a>

            <a href="{{ route('sales.index') }}" 
               class="flex items-center gap-3 py-2.5 rounded-lg transition-colors group relative shrink-0
               {{ request()->routeIs('sales.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}"
               :class="isExpanded ? 'px-3' : 'w-10 justify-center px-0'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Penjualan (SO)</span>
                <div x-show="!isExpanded" x-cloak class="fixed left-16 ml-2 bg-slate-900 text-white text-xs px-2 py-1.5 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[60] pointer-events-none border border-slate-700">Penjualan</div>
            </a>

            <a href="{{ route('shipments.index') }}" 
               class="flex items-center gap-3 py-2.5 rounded-lg transition-colors group relative shrink-0
               {{ request()->routeIs('shipments.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}"
               :class="isExpanded ? 'px-3' : 'w-10 justify-center px-0'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Pengiriman</span>
                <div x-show="!isExpanded" x-cloak class="fixed left-16 ml-2 bg-slate-900 text-white text-xs px-2 py-1.5 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[60] pointer-events-none border border-slate-700">Pengiriman</div>
            </a>
            <a href="{{ route('returns.index') }}" 
                class="flex items-center gap-3 py-2.5 rounded-lg transition-colors group relative shrink-0 {{ request()->routeIs('returns.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}"
                :class="isExpanded ? 'px-3' : 'w-10 justify-center px-0'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                    
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Retur Penjualan</span>
                <div x-show="!isExpanded" x-cloak class="fixed left-16 ml-2 bg-slate-900 text-white text-xs px-2 py-1.5 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[60] pointer-events-none border border-slate-700">Retur</div>
            </a>

            <div x-show="isExpanded" x-cloak class="mt-6 mb-2 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider shrink-0">
                Keuangan
            </div>
            <div x-show="!isExpanded" x-cloak class="my-2 border-t border-slate-700 w-6 shrink-0"></div>

            <a href="{{ route('invoices.index') }}" 
               class="flex items-center gap-3 py-2.5 rounded-lg transition-colors group relative shrink-0
               {{ request()->routeIs('invoices.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}"
               :class="isExpanded ? 'px-3' : 'w-10 justify-center px-0'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Invoice</span>
                <div x-show="!isExpanded" x-cloak class="fixed left-16 ml-2 bg-slate-900 text-white text-xs px-2 py-1.5 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[60] pointer-events-none border border-slate-700">Invoice</div>
            </a>

            @if(Auth::user()->role === 'admin')
            <a href="{{ route('reports.index') }}" 
               class="flex items-center gap-3 py-2.5 rounded-lg transition-colors group relative shrink-0
               {{ request()->routeIs('reports.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}"
               :class="isExpanded ? 'px-3' : 'w-10 justify-center px-0'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Laporan</span>
                <div x-show="!isExpanded" x-cloak class="fixed left-16 ml-2 bg-slate-900 text-white text-xs px-2 py-1.5 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[60] pointer-events-none border border-slate-700">Laporan</div>
            </a>
            @endif

            <div x-show="isExpanded" x-cloak class="mt-6 mb-2 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider shrink-0">
                Master Data
            </div>
            <div x-show="!isExpanded" x-cloak class="my-2 border-t border-slate-700 w-6 shrink-0"></div>

            <a href="{{ route('suppliers.index') }}" 
               class="flex items-center gap-3 py-2.5 rounded-lg transition-colors group relative shrink-0
               {{ request()->routeIs('suppliers.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}"
               :class="isExpanded ? 'px-3' : 'w-10 justify-center px-0'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Suppliers</span>
                <div x-show="!isExpanded" x-cloak class="fixed left-16 ml-2 bg-slate-900 text-white text-xs px-2 py-1.5 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[60] pointer-events-none border border-slate-700">Suppliers</div>
            </a>

            <a href="{{ route('customers.index') }}" 
               class="flex items-center gap-3 py-2.5 rounded-lg transition-colors group relative shrink-0
               {{ request()->routeIs('customers.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}"
               :class="isExpanded ? 'px-3' : 'w-10 justify-center px-0'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Customers</span>
                <div x-show="!isExpanded" x-cloak class="fixed left-16 ml-2 bg-slate-900 text-white text-xs px-2 py-1.5 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[60] pointer-events-none border border-slate-700">Customers</div>
            </a>

            @if(Auth::user()->role === 'admin')
            <a href="{{ route('users.index') }}" 
               class="flex items-center gap-3 py-2.5 rounded-lg transition-colors group relative shrink-0
               {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}"
               :class="isExpanded ? 'px-3' : 'w-10 justify-center px-0'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Users</span>
                <div x-show="!isExpanded" x-cloak class="fixed left-16 ml-2 bg-slate-900 text-white text-xs px-2 py-1.5 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[60] pointer-events-none border border-slate-700">Users</div>
            </a>
            @endif

            @if(Auth::user()->role === 'admin')
            <div x-show="isExpanded" x-cloak class="mt-6 mb-2 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider shrink-0">
                System
            </div>
            <div x-show="!isExpanded" x-cloak class="my-2 border-t border-slate-700 w-6 shrink-0"></div>

            <a href="{{ route('system.backup') }}" 
               class="flex items-center gap-3 py-2.5 rounded-lg transition-colors group relative shrink-0 text-slate-400 hover:text-emerald-400 hover:bg-slate-800"
               :class="isExpanded ? 'px-3' : 'w-10 justify-center px-0'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">System & Backup</span>
                <div x-show="!isExpanded" x-cloak class="fixed left-16 ml-2 bg-slate-900 text-white text-xs px-2 py-1.5 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[60] pointer-events-none border border-slate-700">Maintenance</div>
            </a>
            @endif

        </div>

        <div class="p-3 bg-slate-950 shrink-0 sticky bottom-0 z-10 border-t border-slate-900">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" 
                        class="flex items-center gap-3 w-full py-2.5 text-slate-400 hover:text-red-400 hover:bg-slate-800 rounded-lg transition-colors group relative shrink-0"
                        :class="isExpanded ? 'px-3' : 'justify-center px-0'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Logout</span>
                    <div x-show="!isExpanded" x-cloak class="fixed left-16 ml-2 bg-slate-900 text-white text-xs px-2 py-1.5 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[60] pointer-events-none border border-slate-700">Logout</div>
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-h-screen transition-all duration-300 ease-in-out"
         :class="isExpanded ? 'lg:ml-64' : 'lg:ml-20'">
        
        <header class="bg-white shadow-sm border-b border-slate-200 h-16 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-30">
            
            <div class="flex items-center gap-4">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden text-slate-500 hover:text-slate-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <button @click="isExpanded = !isExpanded" class="hidden lg:block text-slate-400 hover:text-indigo-600 transition-colors">
                    <svg class="w-6 h-6 transform transition-transform duration-300" :class="isExpanded ? 'rotate-0' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                    </svg>
                </button>
                
                <span class="lg:hidden font-bold text-lg text-slate-900">WMS<span class="text-indigo-600">Pro</span></span>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden md:block text-right">
                    <div class="text-sm font-bold text-slate-800">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-slate-500">{{ Auth::user()->role == 'admin' ? 'Administrator' : 'Staff Gudang' }}</div>
                </div>
                <a href="{{ route('profile.edit') }}" class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold hover:ring-2 hover:ring-indigo-300 transition-all">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </a>
            </div>
        </header>

        <main class="flex-1 p-4 lg:p-8 overflow-x-hidden">
            <div class="mx-auto max-w-[1920px]">
                {{ $slot }}
            </div>
        </main>

        <footer class="bg-white border-t border-slate-200 py-4 px-6 text-center text-sm text-slate-500">
            &copy; {{ date('Y') }} WMS Gudang System.
        </footer>
    </div>

</body>
</html>