<!DOCTYPE html>
<html lang="id"
    x-data="{
        darkMode: localStorage.getItem('darkMode') === 'true',
        sidebarOpen: false,
        sidebarCollapsed: false,
        get isMobile() { return window.innerWidth < 1024; }
    }"
    x-init="
        sidebarOpen = window.innerWidth >= 1024;
        $watch('darkMode', v => localStorage.setItem('darkMode', v));
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) { sidebarOpen = true; }
            else { sidebarOpen = false; }
        });
    "
    :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Dashboard') — PAMSIMAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode:'class',
            theme:{
                extend:{
                    colors:{
                        brand:{50:'#eff8ff',100:'#dbeffe',200:'#bfe3fd',300:'#93d0fb',400:'#60b4f7',500:'#3b93f2',600:'#2574e6',700:'#1d5fd4',800:'#1e4dab',900:'#1e4287',950:'#172a53'}
                    },
                    fontFamily:{
                        display:['"Plus Jakarta Sans"','sans-serif'],
                        body:['"DM Sans"','sans-serif']
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        *{font-family:'DM Sans',sans-serif;}
        h1,h2,h3,h4,h5{font-family:'Plus Jakarta Sans',sans-serif;}
        ::-webkit-scrollbar{width:5px;height:5px;}
        ::-webkit-scrollbar-track{background:transparent;}
        ::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:99px;}
        .dark ::-webkit-scrollbar-thumb{background:#334155;}

        /* Sidebar transition */
        #sidebar{
            transition:transform 0.3s cubic-bezier(.4,0,.2,1), width 0.25s ease;
            will-change:transform;
        }

        /* Active sidebar link indicator */
        .sidebar-link.active{position:relative;}
        .sidebar-link.active::before{
            content:'';position:absolute;left:0;top:4px;bottom:4px;
            width:3px;background:linear-gradient(to bottom,#3b93f2,#2574e6);
            border-radius:0 4px 4px 0;
        }
        .sidebar-link{transition:background 0.15s,color 0.15s,transform 0.15s;}

        /* Stat card hover */
        .stat-card{transition:transform 0.2s ease,box-shadow 0.2s ease;}
        .stat-card:hover{transform:translateY(-3px);}

        /* Mobile overlay */
        #sidebar-overlay{transition:opacity 0.3s ease;}

        /* Sidebar scroll fix */
        #sidebar {
            height: 100vh;
            height: 100dvh; /* dynamic viewport height untuk mobile */
        }
       #sidebar nav {
    overflow-y: auto;
    overscroll-behavior: contain;
        }

        /* Fix sidebar tidak ikut scroll */
        html {
            overflow: hidden;
            height: 100%;
        }
        body {
            overflow-y: auto;
            height: 100%;
        }
        /* Pertahankan posisi scroll sidebar */
        #sidebar nav {
            scroll-behavior: auto;
        }   
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 dark:bg-gray-950 text-gray-800 dark:text-gray-200 min-h-screen">

{{-- ==================== MOBILE OVERLAY ==================== --}}
{{-- FIX: overlay yang benar-benar menutup dan hanya tampil di mobile --}}
<div id="sidebar-overlay"
    x-show="sidebarOpen && window.innerWidth < 1024"
    @click="sidebarOpen = false"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-black/60 backdrop-blur-sm z-30 lg:hidden"
    style="display:none">
</div>

{{-- ==================== SIDEBAR ==================== --}}
{{-- FIX: translate-x menggunakan binding langsung tanpa kondisi nested --}}
<aside id="sidebar"
    :class="{
        '-translate-x-full': !sidebarOpen,
        'translate-x-0':     sidebarOpen,
        'w-16': sidebarCollapsed,
        'w-72': !sidebarCollapsed
    }"
   class="fixed top-0 left-0 h-screen bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 z-40 flex flex-col shadow-2xl lg:shadow-none overflow-hidden" style="position:fixed;top:0;left:0;height:100vh;"
    @click.stop>

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-4 h-16 border-b border-gray-100 dark:border-gray-800 flex-shrink-0">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center shadow-md flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
        </div>
        <div x-show="!sidebarCollapsed" class="overflow-hidden flex-1">
            <p class="font-bold text-brand-800 dark:text-white text-sm leading-tight">PAMSIMAS</p>
            <p class="text-gray-400 text-xs">Air Minum Masyarakat</p>
        </div>
        {{-- Tombol tutup sidebar di mobile --}}
        <button x-show="!sidebarCollapsed"
            @click="sidebarOpen = false"
            class="lg:hidden p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 flex-shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- User badge --}}
    <div x-show="!sidebarCollapsed" class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
        <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-brand-50 dark:bg-brand-900/30">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="overflow-hidden">
                <p class="text-gray-800 dark:text-gray-200 text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                <p class="text-brand-600 dark:text-brand-400 text-xs capitalize">{{ auth()->user()->role }}</p>
            </div>
        </div>
    </div>

    {{-- Nav Links --}}
    <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-0.5 scroll-smooth">

        @if(auth()->user()->role === 'admin')
        {{-- ===== ADMIN MENU ===== --}}
        @php
        $adminSections = [
            ['group'=>null, 'items'=>[
                ['r'=>'admin.dashboard','l'=>'Dashboard','i'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ]],
            ['group'=>'Manajemen', 'items'=>[
                ['r'=>'admin.pelanggan.index','l'=>'Pelanggan','i'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['r'=>'admin.petugas.index','l'=>'Petugas','i'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                ['r'=>'admin.assign-petugas.index','l'=>'Assign Petugas','i'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                ['r'=>'admin.jorong.index','l'=>'Kelola Jorong','i'=>'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7'],
                ['r'=>'admin.users.index','l'=>'Manajemen User','i'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                // ['r'=>'admin.registrasi.index','l'=>'Pendaftaran','i'=>'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z','badge'=>true],
            ]],
            ['group'=>'Keuangan', 'items'=>[
                ['r'=>'admin.tagihan.index','l'=>'Tagihan','i'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['r'=>'admin.pembayaran.index','l'=>'Pembayaran','i'=>'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                ['r'=>'admin.laporan.index','l'=>'Laporan','i'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ]],
            ['group'=>'Layanan', 'items'=>[
                ['r'=>'admin.pengaduan.index','l'=>'Pengaduan','i'=>'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
                ['r'=>'admin.notifikasi.index','l'=>'Notifikasi','i'=>'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9','badge'=>'notif'],
                ['r'=>'admin.log.index','l'=>'Log Aktivitas','i'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                ['r'=>'admin.pengaturan.index','l'=>'Pengaturan','i'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ]],
        ];
        @endphp

        @foreach($adminSections as $section)
            @if($section['group'])
            <p x-show="!sidebarCollapsed" class="px-3 pt-3 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ $section['group'] }}</p>
            @endif
            @foreach($section['items'] as $item)
            @php
                $isActive = request()->routeIs($item['r'].'*');
                $badgeCount = 0;
                if(isset($item['badge'])){
                    if($item['badge']==='notif') $badgeCount = auth()->user()->notifBelumDibaca();
                    elseif($item['badge']===true){
                        $badgeCount = \App\Models\Pelanggan::where('status_registrasi','pending')->count()
                                    + \App\Models\Petugas::where('status_registrasi','pending')->count();
                    }
                }
            @endphp
            <a href="{{ route($item['r']) }}"
                @click="if(window.innerWidth < 1024) sidebarOpen = false"
                class="sidebar-link {{ $isActive ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
                {{ $isActive ? 'bg-brand-50 dark:bg-brand-900/40 text-brand-700 dark:text-brand-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['i'] }}"/>
                </svg>
                <span x-show="!sidebarCollapsed" class="flex-1 truncate">{{ $item['l'] }}</span>
                @if($badgeCount > 0)
                <span x-show="!sidebarCollapsed"
                    class="flex-shrink-0 w-5 h-5 {{ $item['badge']==='notif' ? 'bg-red-500' : 'bg-amber-500' }} text-white text-xs rounded-full flex items-center justify-center font-bold">
                    {{ min($badgeCount, 9) }}
                </span>
                @endif
            </a>
            @endforeach
        @endforeach
        @endif

        @if(auth()->user()->role === 'petugas')
        {{-- ===== PETUGAS MENU ===== --}}
        @php
        $petugasLinks = [
            ['r'=>'petugas.dashboard',      'l'=>'Dashboard',    'i'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['r'=>'petugas.meteran.index',  'l'=>'Input Meteran','i'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
            ['r'=>'petugas.peta.index', 'l'=>'Peta Pelanggan', 'i'=>'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7'],
            ['r'=>'petugas.riwayat.index',  'l'=>'Riwayat',      'i'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['r'=>'petugas.pengaduan.index','l'=>'Pengaduan',    'i'=>'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
            
        ];
        @endphp
        <p x-show="!sidebarCollapsed" class="px-3 pt-2 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Menu Petugas</p>
        @foreach($petugasLinks as $item)
        <a href="{{ route($item['r']) }}"
            @click="if(window.innerWidth < 1024) sidebarOpen = false"
            class="sidebar-link {{ request()->routeIs($item['r'].'*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
            {{ request()->routeIs($item['r'].'*') ? 'bg-brand-50 dark:bg-brand-900/40 text-brand-700 dark:text-brand-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['i'] }}"/>
            </svg>
            <span x-show="!sidebarCollapsed">{{ $item['l'] }}</span>
        </a>
        @endforeach
        @endif

        @if(auth()->user()->role === 'pelanggan')
        {{-- ===== PELANGGAN MENU ===== --}}
        @php
        $pelangganLinks = [
            ['r'=>'pelanggan.dashboard',       'l'=>'Dashboard',   'i'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['r'=>'pelanggan.tagihan.index',   'l'=>'Tagihan Saya','i'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['r'=>'pelanggan.riwayat.index',   'l'=>'Riwayat',     'i'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['r'=>'pelanggan.pengaduan.index', 'l'=>'Pengaduan',   'i'=>'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
        ];
        @endphp
        <p x-show="!sidebarCollapsed" class="px-3 pt-2 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">Menu Pelanggan</p>
        @foreach($pelangganLinks as $item)
        <a href="{{ route($item['r']) }}"
            @click="if(window.innerWidth < 1024) sidebarOpen = false"
            class="sidebar-link {{ request()->routeIs($item['r'].'*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all
            {{ request()->routeIs($item['r'].'*') ? 'bg-brand-50 dark:bg-brand-900/40 text-brand-700 dark:text-brand-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['i'] }}"/>
            </svg>
            <span x-show="!sidebarCollapsed">{{ $item['l'] }}</span>
        </a>
        @endforeach
        @endif

    </nav>

    {{-- Collapse button (desktop only) --}}
    <div class="p-3 border-t border-gray-100 dark:border-gray-800 hidden lg:block">
        <button @click="sidebarCollapsed = !sidebarCollapsed"
            class="flex w-full items-center justify-center gap-2 py-2 rounded-xl text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all text-sm">
            <svg :class="sidebarCollapsed ? 'rotate-180' : ''" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
            <span x-show="!sidebarCollapsed" class="text-xs"></span>
        </button>
    </div>
</aside>

{{-- ==================== MAIN CONTENT ==================== --}}
<div :class="{ 'lg:ml-72': !sidebarCollapsed, 'lg:ml-16': sidebarCollapsed }"
    class="transition-all duration-300 min-h-screen flex flex-col">

    {{-- ===== NAVBAR ===== --}}
    <header class="sticky top-0 z-20 bg-white/95 dark:bg-gray-900/95 backdrop-blur border-b border-gray-100 dark:border-gray-800 h-16 flex items-center px-4 sm:px-6 gap-3 shadow-sm">

        {{-- Hamburger — hanya mobile --}}
        <button @click="sidebarOpen = !sidebarOpen"
            class="p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all lg:hidden touch-manipulation">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Page title --}}
        <div class="flex-1 min-w-0">
            <h1 class="text-base font-bold text-gray-800 dark:text-white truncate">@yield('page_title','Dashboard')</h1>
            <p class="text-gray-400 text-xs hidden sm:block truncate">@yield('page_subtitle','')</p>
        </div>

        {{-- Right Actions --}}
        <div class="flex items-center gap-1 flex-shrink-0">

            {{-- Dark mode --}}
            <button @click="darkMode = !darkMode"
                class="p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">
                <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
                <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </button>

            {{-- Notifikasi dropdown --}}
            @php $notifCount = auth()->user()->notifBelumDibaca(); @endphp
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="relative p-2 rounded-xl text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all touch-manipulation">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @if($notifCount > 0)
                    <span class="absolute top-0.5 right-0.5 w-4 h-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold leading-none">
                        {{ $notifCount > 9 ? '9+' : $notifCount }}
                    </span>
                    @endif
                </button>

                <div x-show="open" @click.outside="open = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-80 max-w-[calc(100vw-2rem)] bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 z-50 overflow-hidden"
                    style="display:none">

                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <p class="font-semibold text-gray-800 dark:text-white text-sm">Notifikasi</p>
                            @if($notifCount > 0)
                            <span class="px-2 py-0.5 bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 text-xs rounded-full font-semibold">{{ $notifCount }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            @if($notifCount > 0)
                            <form method="POST" action="{{ route('admin.notifikasi.baca-semua') }}">
                                @csrf
                                <button type="submit" class="text-xs text-brand-600 dark:text-brand-400 hover:underline font-medium">Baca semua</button>
                            </form>
                            @endif
                            @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.notifikasi.index') }}" @click="open=false" class="text-xs text-gray-400 hover:text-brand-600">Semua →</a>
                            @endif
                        </div>
                    </div>

                    <div class="max-h-72 overflow-y-auto divide-y divide-gray-50 dark:divide-gray-800">
                        @php $notifList = auth()->user()->notifikasi()->latest()->take(6)->get(); @endphp
                        @forelse($notifList as $n)
                        <div class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors {{ !$n->sudah_dibaca ? 'bg-brand-50/40 dark:bg-brand-900/10' : '' }}">
                            <div class="w-8 h-8 rounded-lg flex-shrink-0 flex items-center justify-center mt-0.5
                                {{ $n->tipe==='success' ? 'bg-emerald-100 dark:bg-emerald-900/40'
                                 : ($n->tipe==='warning' ? 'bg-amber-100 dark:bg-amber-900/40'
                                 : ($n->tipe==='error'   ? 'bg-red-100 dark:bg-red-900/40'
                                 : 'bg-brand-100 dark:bg-brand-900/40')) }}">
                                @if($n->tipe==='success')<svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @elseif($n->tipe==='warning')<svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                @elseif($n->tipe==='error')<svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                @else<svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>@endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 leading-tight">
                                    {{ $n->judul }}
                                    @if(!$n->sudah_dibaca)<span class="inline-block w-1.5 h-1.5 rounded-full bg-brand-500 ml-1 align-middle"></span>@endif
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2">{{ $n->pesan }}</p>
                                <p class="text-xs text-gray-300 dark:text-gray-600 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="px-4 py-10 text-center">
                            <svg class="w-10 h-10 text-gray-200 dark:text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <p class="text-gray-400 text-sm">Tidak ada notifikasi</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Profile dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-all touch-manipulation">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="text-sm font-semibold text-gray-800 dark:text-white leading-tight">{{ Str::limit(auth()->user()->name, 12) }}</p>
                        <p class="text-xs text-gray-400 capitalize">{{ auth()->user()->role }}</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" @click.outside="open = false"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-52 bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 z-50 overflow-hidden"
                    style="display:none">
                    <div class="p-3 border-b border-gray-100 dark:border-gray-800">
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                        <span class="inline-block mt-1.5 px-2 py-0.5 rounded-full text-xs font-semibold capitalize
                            {{ auth()->user()->role==='admin' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300'
                             : (auth()->user()->role==='petugas' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                             : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300') }}">
                            {{ auth()->user()->role }}
                        </span>
                    </div>
                    <div class="p-1.5">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- ===== PAGE CONTENT ===== --}}
    <main class="flex-1 p-4 sm:p-6">

        @if(session('success'))
        <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,4500)"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="mb-4 flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 rounded-xl shadow-sm">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium flex-1">{{ session('success') }}</p>
            <button @click="show=false" class="text-emerald-500 hover:text-emerald-700 flex-shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        @endif

        @if(session('error'))
        <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,5000)"
            class="mb-4 flex items-center gap-3 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-xl shadow-sm">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium flex-1">{{ session('error') }}</p>
            <button @click="show=false" class="text-red-500 hover:text-red-700 flex-shrink-0"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-red-800 dark:text-red-300">
                    <p class="font-semibold text-sm mb-1">Terdapat kesalahan:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-sm">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        @yield('content')
    </main>

    <footer class="border-t border-gray-100 dark:border-gray-800 px-6 py-3 flex items-center justify-between">
        <p class="text-xs text-gray-400">© {{ date('Y') }} PAMSIMAS — Sistem Air Minum Berbasis Masyarakat</p>
        <p class="text-xs text-gray-300 dark:text-gray-700">v1.0.0</p>
    </footer>
</div>

@stack('scripts')
<script>
// Simpan & restore posisi scroll sidebar
const sidebarNav = document.querySelector('#sidebar nav');
if (sidebarNav) {
    // Restore posisi scroll
    const savedScroll = sessionStorage.getItem('sidebarScroll');
    if (savedScroll) {
        sidebarNav.scrollTop = parseInt(savedScroll);
    }

    // Simpan posisi scroll saat klik link
    sidebarNav.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function() {
            sessionStorage.setItem('sidebarScroll', sidebarNav.scrollTop);
        });
    });
}
</script>
</body>
</html>
