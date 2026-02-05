<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    <aside class="fixed inset-y-0 left-0 z-50 bg-gradient-to-b from-slate-900 to-slate-950 text-white transition-all duration-300 ease-in-out flex flex-col shadow-2xl border-r border-slate-800"
           :class="[
               mobileMenuOpen ? 'translate-x-0 w-64' : '-translate-x-full lg:translate-x-0',
               isExpanded ? 'lg:w-64' : 'lg:w-20'
           ]">
        
        <div class="h-16 flex items-center shrink-0 bg-slate-950/50 sticky top-0 z-10 backdrop-blur-md border-b border-white/5"
             :class="isExpanded ? 'px-6' : 'justify-center px-0'">
            <div class="flex items-center gap-3 overflow-hidden whitespace-nowrap">
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-1.5 rounded-lg shrink-0 shadow-lg shadow-indigo-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path></svg>
                </div>
                <div x-show="isExpanded" x-cloak class="flex flex-col">
                    <span class="font-bold text-lg tracking-wide text-white leading-tight">
                        WMS<span class="text-indigo-400">Enterprise</span>
                    </span>
                    <span class="text-[9px] text-slate-400 uppercase tracking-widest">System v2.0</span>
                </div>
            </div>
        </div>

        <div class="flex-1 py-6 flex flex-col gap-1 overflow-y-auto overflow-x-hidden"
             :class="[isExpanded ? 'px-0 sidebar-scroll' : 'px-2 items-center no-scrollbar']">
            
            <a href="{{ route('dashboard') }}" 
               x-data="{ tooltipTop: 0 }"
               @mouseenter="if(!isExpanded) tooltipTop = $el.getBoundingClientRect().top"
               class="flex items-center gap-3 py-3 mx-2 rounded-lg transition-all duration-200 group relative shrink-0
               {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white border-l-4 border-indigo-500 pl-3 shadow-inner' : 'text-slate-400 hover:text-white hover:bg-white/5 pl-4 border-l-4 border-transparent' }}"
               :class="isExpanded ? '' : 'justify-center px-0 pl-0 border-l-0 w-12'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Executive Dashboard</span>
                
                <template x-if="!isExpanded">
                    <div class="fixed left-20 bg-slate-800 text-white text-xs px-3 py-2 rounded-md shadow-2xl border border-slate-600 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[9999] pointer-events-none font-semibold"
                         :style="'top: ' + (tooltipTop + 10) + 'px'">
                        Ringkasan Bisnis
                    </div>
                </template>
            </a>

            <div x-show="isExpanded" x-cloak class="mt-6 mb-2 px-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest shrink-0">
                Core Business
            </div>
            <div x-show="!isExpanded" x-cloak class="my-3 border-t border-white/10 w-8 shrink-0"></div>

            <a href="{{ route('products.index') }}" 
               x-data="{ tooltipTop: 0 }"
               @mouseenter="if(!isExpanded) tooltipTop = $el.getBoundingClientRect().top"
               class="flex items-center gap-3 py-3 mx-2 rounded-lg transition-all duration-200 group relative shrink-0
               {{ request()->routeIs('products.*') ? 'bg-white/10 text-white border-l-4 border-indigo-500 pl-3' : 'text-slate-400 hover:text-white hover:bg-white/5 pl-4 border-l-4 border-transparent' }}"
               :class="isExpanded ? '' : 'justify-center px-0 pl-0 border-l-0 w-12'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Inventory & Stok</span>
                <template x-if="!isExpanded">
                    <div class="fixed left-20 bg-slate-800 text-white text-xs px-3 py-2 rounded-md shadow-2xl border border-slate-600 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[9999] pointer-events-none"
                         :style="'top: ' + (tooltipTop + 10) + 'px'">
                        Manajemen Produk
                    </div>
                </template>
            </a>

            <a href="{{ route('purchases.index') }}" 
               x-data="{ tooltipTop: 0 }"
               @mouseenter="if(!isExpanded) tooltipTop = $el.getBoundingClientRect().top"
               class="flex items-center gap-3 py-3 mx-2 rounded-lg transition-all duration-200 group relative shrink-0
               {{ request()->routeIs('purchases.*') ? 'bg-white/10 text-white border-l-4 border-indigo-500 pl-3' : 'text-slate-400 hover:text-white hover:bg-white/5 pl-4 border-l-4 border-transparent' }}"
               :class="isExpanded ? '' : 'justify-center px-0 pl-0 border-l-0 w-12'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Procurement (PO)</span>
                <template x-if="!isExpanded">
                    <div class="fixed left-20 bg-slate-800 text-white text-xs px-3 py-2 rounded-md shadow-2xl border border-slate-600 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[9999] pointer-events-none"
                         :style="'top: ' + (tooltipTop + 10) + 'px'">
                        Purchase Order
                    </div>
                </template>
            </a>

            <a href="{{ route('sales.index') }}" 
               x-data="{ tooltipTop: 0 }"
               @mouseenter="if(!isExpanded) tooltipTop = $el.getBoundingClientRect().top"
               class="flex items-center gap-3 py-3 mx-2 rounded-lg transition-all duration-200 group relative shrink-0
               {{ request()->routeIs('sales.*') ? 'bg-white/10 text-white border-l-4 border-indigo-500 pl-3' : 'text-slate-400 hover:text-white hover:bg-white/5 pl-4 border-l-4 border-transparent' }}"
               :class="isExpanded ? '' : 'justify-center px-0 pl-0 border-l-0 w-12'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Sales Order (SO)</span>
                <template x-if="!isExpanded">
                    <div class="fixed left-20 bg-slate-800 text-white text-xs px-3 py-2 rounded-md shadow-2xl border border-slate-600 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[9999] pointer-events-none"
                         :style="'top: ' + (tooltipTop + 10) + 'px'">
                        Penjualan Barang
                    </div>
                </template>
            </a>

            <div x-show="isExpanded" x-cloak class="mt-6 mb-2 px-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest shrink-0">
                Logistics & Return
            </div>
            <div x-show="!isExpanded" x-cloak class="my-3 border-t border-white/10 w-8 shrink-0"></div>

            <a href="{{ route('shipments.index') }}" 
               x-data="{ tooltipTop: 0 }"
               @mouseenter="if(!isExpanded) tooltipTop = $el.getBoundingClientRect().top"
               class="flex items-center gap-3 py-3 mx-2 rounded-lg transition-all duration-200 group relative shrink-0
               {{ request()->routeIs('shipments.*') ? 'bg-white/10 text-white border-l-4 border-indigo-500 pl-3' : 'text-slate-400 hover:text-white hover:bg-white/5 pl-4 border-l-4 border-transparent' }}"
               :class="isExpanded ? '' : 'justify-center px-0 pl-0 border-l-0 w-12'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Pengiriman (Shipment)</span>
                <template x-if="!isExpanded">
                    <div class="fixed left-20 bg-slate-800 text-white text-xs px-3 py-2 rounded-md shadow-2xl border border-slate-600 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[9999] pointer-events-none"
                         :style="'top: ' + (tooltipTop + 10) + 'px'">
                        Logistik Keluar
                    </div>
                </template>
            </a>

            <a href="{{ route('purchase_returns.index') }}" 
               x-data="{ tooltipTop: 0 }"
               @mouseenter="if(!isExpanded) tooltipTop = $el.getBoundingClientRect().top"
               class="flex items-center gap-3 py-3 mx-2 rounded-lg transition-all duration-200 group relative shrink-0
               {{ request()->routeIs('purchase_returns.*') ? 'bg-white/10 text-white border-l-4 border-indigo-500 pl-3' : 'text-slate-400 hover:text-white hover:bg-white/5 pl-4 border-l-4 border-transparent' }}"
               :class="isExpanded ? '' : 'justify-center px-0 pl-0 border-l-0 w-12'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Retur Supplier</span>
                <template x-if="!isExpanded">
                    <div class="fixed left-20 bg-slate-800 text-white text-xs px-3 py-2 rounded-md shadow-2xl border border-slate-600 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[9999] pointer-events-none"
                         :style="'top: ' + (tooltipTop + 10) + 'px'">
                        Retur Pembelian
                    </div>
                </template>
            </a>

            <a href="{{ route('returns.index') }}" 
               x-data="{ tooltipTop: 0 }"
               @mouseenter="if(!isExpanded) tooltipTop = $el.getBoundingClientRect().top"
               class="flex items-center gap-3 py-3 mx-2 rounded-lg transition-all duration-200 group relative shrink-0
               {{ request()->routeIs('returns.*') ? 'bg-white/10 text-white border-l-4 border-indigo-500 pl-3' : 'text-slate-400 hover:text-white hover:bg-white/5 pl-4 border-l-4 border-transparent' }}"
               :class="isExpanded ? '' : 'justify-center px-0 pl-0 border-l-0 w-12'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Retur Customer</span>
                <template x-if="!isExpanded">
                    <div class="fixed left-20 bg-slate-800 text-white text-xs px-3 py-2 rounded-md shadow-2xl border border-slate-600 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[9999] pointer-events-none"
                         :style="'top: ' + (tooltipTop + 10) + 'px'">
                        Retur Penjualan
                    </div>
                </template>
            </a>

            <div x-show="isExpanded" x-cloak class="mt-6 mb-2 px-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest shrink-0">
                Finance & Accounting
            </div>
            <div x-show="!isExpanded" x-cloak class="my-3 border-t border-white/10 w-8 shrink-0"></div>

            <a href="{{ route('invoices.index') }}" 
               x-data="{ tooltipTop: 0 }"
               @mouseenter="if(!isExpanded) tooltipTop = $el.getBoundingClientRect().top"
               class="flex items-center gap-3 py-3 mx-2 rounded-lg transition-all duration-200 group relative shrink-0
               {{ request()->routeIs('invoices.*') ? 'bg-white/10 text-white border-l-4 border-indigo-500 pl-3' : 'text-slate-400 hover:text-white hover:bg-white/5 pl-4 border-l-4 border-transparent' }}"
               :class="isExpanded ? '' : 'justify-center px-0 pl-0 border-l-0 w-12'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Invoice & Piutang</span>
                <template x-if="!isExpanded">
                    <div class="fixed left-20 bg-slate-800 text-white text-xs px-3 py-2 rounded-md shadow-2xl border border-slate-600 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[9999] pointer-events-none"
                         :style="'top: ' + (tooltipTop + 10) + 'px'">
                        Tagihan Pelanggan
                    </div>
                </template>
            </a>

            <a href="{{ route('finance.index') }}" 
               x-data="{ tooltipTop: 0 }"
               @mouseenter="if(!isExpanded) tooltipTop = $el.getBoundingClientRect().top"
               class="flex items-center gap-3 py-3 mx-2 rounded-lg transition-all duration-200 group relative shrink-0
               {{ request()->routeIs('finance.*') ? 'bg-white/10 text-white border-l-4 border-indigo-500 pl-3' : 'text-slate-400 hover:text-white hover:bg-white/5 pl-4 border-l-4 border-transparent' }}"
               :class="isExpanded ? '' : 'justify-center px-0 pl-0 border-l-0 w-12'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Treasury (Kas)</span>
                <template x-if="!isExpanded">
                    <div class="fixed left-20 bg-slate-800 text-white text-xs px-3 py-2 rounded-md shadow-2xl border border-slate-600 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[9999] pointer-events-none"
                         :style="'top: ' + (tooltipTop + 10) + 'px'">
                        Manajemen Keuangan
                    </div>
                </template>
            </a>

            <div x-show="isExpanded" x-cloak class="mt-6 mb-2 px-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest shrink-0">
                Business Partners
            </div>
            <div x-show="!isExpanded" x-cloak class="my-3 border-t border-white/10 w-8 shrink-0"></div>

            <a href="{{ route('suppliers.index') }}" 
               x-data="{ tooltipTop: 0 }"
               @mouseenter="if(!isExpanded) tooltipTop = $el.getBoundingClientRect().top"
               class="flex items-center gap-3 py-3 mx-2 rounded-lg transition-all duration-200 group relative shrink-0
               {{ request()->routeIs('suppliers.*') ? 'bg-white/10 text-white border-l-4 border-indigo-500 pl-3' : 'text-slate-400 hover:text-white hover:bg-white/5 pl-4 border-l-4 border-transparent' }}"
               :class="isExpanded ? '' : 'justify-center px-0 pl-0 border-l-0 w-12'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Data Supplier</span>
                <template x-if="!isExpanded">
                    <div class="fixed left-20 bg-slate-800 text-white text-xs px-3 py-2 rounded-md shadow-2xl border border-slate-600 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[9999] pointer-events-none"
                         :style="'top: ' + (tooltipTop + 10) + 'px'">
                        Mitra Pemasok
                    </div>
                </template>
            </a>

            <a href="{{ route('customers.index') }}" 
               x-data="{ tooltipTop: 0 }"
               @mouseenter="if(!isExpanded) tooltipTop = $el.getBoundingClientRect().top"
               class="flex items-center gap-3 py-3 mx-2 rounded-lg transition-all duration-200 group relative shrink-0
               {{ request()->routeIs('customers.*') ? 'bg-white/10 text-white border-l-4 border-indigo-500 pl-3' : 'text-slate-400 hover:text-white hover:bg-white/5 pl-4 border-l-4 border-transparent' }}"
               :class="isExpanded ? '' : 'justify-center px-0 pl-0 border-l-0 w-12'">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Data Pelanggan</span>
                <template x-if="!isExpanded">
                    <div class="fixed left-20 bg-slate-800 text-white text-xs px-3 py-2 rounded-md shadow-2xl border border-slate-600 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[9999] pointer-events-none"
                         :style="'top: ' + (tooltipTop + 10) + 'px'">
                        Daftar Customer
                    </div>
                </template>
            </a>

            @hasanyrole('super_admin|admin|manager')
                <div x-show="isExpanded" x-cloak class="mt-6 mb-2 px-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest shrink-0">
                    Administration
                </div>
                <div x-show="!isExpanded" x-cloak class="my-3 border-t border-white/10 w-8 shrink-0"></div>

                <a href="{{ route('reports.index') }}" 
                   x-data="{ tooltipTop: 0 }"
                   @mouseenter="if(!isExpanded) tooltipTop = $el.getBoundingClientRect().top"
                   class="flex items-center gap-3 py-3 mx-2 rounded-lg transition-all duration-200 group relative shrink-0
                   {{ request()->routeIs('reports.*') ? 'bg-white/10 text-white border-l-4 border-indigo-500 pl-3' : 'text-slate-400 hover:text-white hover:bg-white/5 pl-4 border-l-4 border-transparent' }}"
                   :class="isExpanded ? '' : 'justify-center px-0 pl-0 border-l-0 w-12'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                    <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Laporan & Analisa</span>
                    <template x-if="!isExpanded">
                        <div class="fixed left-20 bg-slate-800 text-white text-xs px-3 py-2 rounded-md shadow-2xl border border-slate-600 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[9999] pointer-events-none"
                             :style="'top: ' + (tooltipTop + 10) + 'px'">
                            Financial & Stock Report
                        </div>
                    </template>
                </a>

                @hasanyrole('super_admin|admin')
                    <a href="{{ route('users.index') }}" 
                       x-data="{ tooltipTop: 0 }"
                       @mouseenter="if(!isExpanded) tooltipTop = $el.getBoundingClientRect().top"
                       class="flex items-center gap-3 py-3 mx-2 rounded-lg transition-all duration-200 group relative shrink-0
                       {{ request()->routeIs('users.*') ? 'bg-white/10 text-white border-l-4 border-indigo-500 pl-3' : 'text-slate-400 hover:text-white hover:bg-white/5 pl-4 border-l-4 border-transparent' }}"
                       :class="isExpanded ? '' : 'justify-center px-0 pl-0 border-l-0 w-12'">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">User Management</span>
                        <template x-if="!isExpanded">
                            <div class="fixed left-20 bg-slate-800 text-white text-xs px-3 py-2 rounded-md shadow-2xl border border-slate-600 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[9999] pointer-events-none"
                                 :style="'top: ' + (tooltipTop + 10) + 'px'">
                                Hak Akses & Staff
                            </div>
                        </template>
                    </a>

                    <a href="{{ route('system.backup') }}" 
                       x-data="{ tooltipTop: 0 }"
                       @mouseenter="if(!isExpanded) tooltipTop = $el.getBoundingClientRect().top"
                       class="flex items-center gap-3 py-3 mx-2 rounded-lg transition-all duration-200 group relative shrink-0 text-slate-400 hover:text-emerald-400 hover:bg-white/5 pl-4 border-l-4 border-transparent"
                       :class="isExpanded ? '' : 'justify-center px-0 pl-0 border-l-0 w-12'">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">System Backup</span>
                        <template x-if="!isExpanded">
                            <div class="fixed left-20 bg-slate-800 text-white text-xs px-3 py-2 rounded-md shadow-2xl border border-slate-600 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[9999] pointer-events-none"
                                 :style="'top: ' + (tooltipTop + 10) + 'px'">
                                Maintenance
                            </div>
                        </template>
                    </a>
                @endhasanyrole
            @endhasanyrole

        </div>

        <div class="p-3 bg-slate-950/80 shrink-0 sticky bottom-0 z-10 border-t border-white/5 backdrop-blur-md">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" 
                        class="flex items-center gap-3 w-full py-3 text-slate-400 hover:text-red-400 hover:bg-white/5 rounded-lg transition-all duration-200 group relative shrink-0 pl-4"
                        :class="isExpanded ? '' : 'justify-center px-0 pl-0'">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span x-show="isExpanded" x-cloak class="text-sm font-medium whitespace-nowrap">Sign Out</span>
                    <template x-if="!isExpanded">
                        <div class="fixed left-20 bg-slate-800 text-white text-xs px-3 py-2 rounded-md shadow-2xl border border-slate-600 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-[9999] pointer-events-none"
                             style="bottom: 20px;">
                            Keluar
                        </div>
                    </template>
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
                    <div class="text-xs text-slate-500 font-medium">
                        {{-- Mengambil Role pertama dari Spatie dan diformat --}}
                        {{ ucwords(str_replace('_', ' ', Auth::user()->getRoleNames()->first() ?? 'Staff')) }}
                    </div>
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