<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: false, mobileMenu: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAMSIMAS Sistem Air Minum Berbasis Masyarakat </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff8ff',
                            100: '#dbeffe',
                            200: '#bfe3fd',
                            300: '#93d0fb',
                            400: '#60b4f7',
                            500: '#3b93f2',
                            600: '#2574e6',
                            700: '#1d5fd4',
                            800: '#1e4dab',
                            900: '#1e4287',
                            950: '#172a53',
                        }
                    },
                    fontFamily: {
                        display: ['"Plus Jakarta Sans"', 'sans-serif'],
                        body: ['"DM Sans"', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-slow': 'float 9s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'slide-up': 'slideUp 0.6s ease-out forwards',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        slideUp: {
                            'from': { opacity: '0', transform: 'translateY(30px)' },
                            'to': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { font-family: 'DM Sans', sans-serif; }
        h1, h2, h3, h4 { font-family: 'Plus Jakarta Sans', sans-serif; }

        .hero-gradient {
            background: linear-gradient(135deg, #172a53 0%, #1e4287 25%, #2574e6 60%, #60b4f7 100%);
        }
        .glass-card {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.15);
        }
        .water-wave {
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 120'%3E%3Cpath fill='%23ffffff' fill-opacity='1' d='M0,64L60,69.3C120,75,240,85,360,80C480,75,600,53,720,48C840,43,960,53,1080,58.7C1200,64,1320,64,1380,64L1440,64L1440,120L1380,120C1320,120,1200,120,1080,120C960,120,840,120,720,120C600,120,480,120,360,120C240,120,120,120,60,120L0,120Z'%3E%3C/path%3E%3C/svg%3E") no-repeat bottom;
            background-size: cover;
        }
        .stat-card:hover { transform: translateY(-4px); }
        .feature-card:hover { transform: translateY(-6px); }
        .step-line::after {
            content: '';
            position: absolute;
            top: 24px;
            left: calc(50% + 24px);
            width: calc(100% - 48px);
            height: 2px;
            background: linear-gradient(to right, #3b93f2, #60b4f7);
            z-index: 0;
        }
        @keyframes ripple {
            0% { transform: scale(1); opacity: 0.4; }
            100% { transform: scale(2.5); opacity: 0; }
        }
        .ripple-effect::before, .ripple-effect::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: rgba(96,180,247,0.3);
            animation: ripple 2.5s ease-out infinite;
        }
        .ripple-effect::after { animation-delay: 1.2s; }
        .counter { font-feature-settings: 'tnum'; }
        .dark .glass-dark {
            background: rgba(23, 42, 83, 0.7);
            border: 1px solid rgba(96,180,247,0.2);
        }
        .testi-card { transition: all 0.3s ease; }
        .testi-card:hover { transform: scale(1.02); }
    </style>
</head>
<body class="bg-white dark:bg-gray-950 transition-colors duration-300" x-data="{ 
    statsVisible: false,
    pelanggan: 0,
    volume: 0,
    pemakaianKumulatif: 0,
    animateStats() {
        this.statsVisible = true;
        this.animateNumber('pelanggan', {{ $totalPelanggan }}, 2000);
        this.animateNumber('volume', {{ $pemakaianBulanIni }}, 2000);
        this.animateNumber('pemakaianKumulatif', {{ $pemakaianKumulatif }}, 2000);
    },
    animateNumber(prop, target, duration) {
        let start = 0;
        const step = target / (duration / 16);
        const timer = setInterval(() => {
            start += step;
            if (start >= target) { this[prop] = target; clearInterval(timer); }
            else { this[prop] = Math.floor(start); }
        }, 16);
    }
}">

    <!-- NAVBAR -->
    <nav class="fixed top-0 w-full z-50 transition-all duration-300" 
         x-data="{ scrolled: false }"
         @scroll.window="scrolled = window.pageYOffset > 50"
         :class="scrolled ? 'bg-black/95 dark:bg-gray-900/95 backdrop-blur shadow-lg' : 'bg-transparent'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-display font-800 text-blue-300 dark:text-white text-sm font-bold tracking-tight">PAMSIMAS</span>
                        <p class="text-xs text-gray-100 dark:text-gray-100 -mt-0.5 hidden sm:block">Sistem Air Minum Masyarakat</p>
                    </div>
                </div>

                <!-- Nav Links Desktop -->
                <div class="hidden md:flex items-center gap-1">
                    <a href="#fitur" class="px-4 py-2 text-sm font-semibold text-gray-100 dark:text-gray-300 hover:text-brand-600 dark:hover:text-brand-400 rounded-lg hover:bg-brand-50 dark:hover:bg-brand-900/30 transition-all">Fitur</a>
                    <a href="#statistik" class="px-4 py-2 text-sm font-semibold text-gray-100 dark:text-gray-300 hover:text-brand-600 dark:hover:text-brand-400 rounded-lg hover:bg-brand-50 dark:hover:bg-brand-900/30 transition-all">Statistik</a>
                    <a href="#cara-kerja" class="px-4 py-2 text-sm font-semibold text-gray-100 dark:text-gray-300 hover:text-brand-600 dark:hover:text-brand-400 rounded-lg hover:bg-brand-50 dark:hover:bg-brand-900/30 transition-all">Cara Kerja</a>
                    <a href="#testimoni" class="px-4 py-2 text-sm font-semibold text-gray-100 dark:text-gray-300 hover:text-brand-600 dark:hover:text-brand-400 rounded-lg hover:bg-brand-50 dark:hover:bg-brand-900/30 transition-all">Testimoni</a>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-2">
                    <!-- Dark Mode Toggle -->
                    <button @click="darkMode = !darkMode" class="p-2 rounded-lg text-gray-100 dark:text-gray-100 hover:bg-gray-800 dark:hover:bg-gray-800 transition-all">
                        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </button>
                    <a href="/login" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow-md hover:shadow-brand-500/30 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Login
                    </a>
                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">
                        <svg x-show="!mobileMenu" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileMenu" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div x-show="mobileMenu" x-transition class="md:hidden bg-white dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 px-4 py-3 space-y-1">
            <a href="#fitur" @click="mobileMenu=false" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-brand-900/30 rounded-lg">Fitur</a>
            <a href="#statistik" @click="mobileMenu=false" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-brand-900/30 rounded-lg">Statistik</a>
            <a href="#cara-kerja" @click="mobileMenu=false" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-brand-900/30 rounded-lg">Cara Kerja</a>
            <a href="#testimoni" @click="mobileMenu=false" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-brand-50 dark:hover:bg-brand-900/30 rounded-lg">Testimoni</a>
            <a href="/login" class="block px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-xl text-center">Login</a>
        </div>
    </nav>

    <!-- ===== HERO SECTION ===== -->
    <section class="relative hero-gradient min-h-screen flex items-center overflow-hidden pt-16">
        <!-- Background Decorations -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-brand-400/20 animate-float-slow"></div>
            <div class="absolute top-1/4 -left-20 w-64 h-64 rounded-full bg-brand-300/15 animate-float"></div>
            <div class="absolute bottom-20 right-1/4 w-48 h-48 rounded-full bg-white/5 animate-pulse-slow"></div>
            <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 40px 40px;"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="text-white">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-card text-sm font-medium text-brand-200 mb-6">
                        <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
                        Sistem Aktif & Terpercaya
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-4 tracking-tight">
                        Sistem Informasi
                        <span class="block text-transparent bg-clip-text bg-gradient-to-r from-brand-200 to-white">PAMSIMAS</span>
                    </h1>
                    <p class="text-xl text-brand-200 font-light mb-3">Sistem Air Minum Berbasis Masyarakat</p>
                    <p class="text-brand-300 text-base leading-relaxed mb-10 max-w-lg">
                        Platform digital terintegrasi untuk pengelolaan air bersih yang efisien, transparan, dan akuntabel — melayani ribuan keluarga setiap harinya.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <a href="/login" class="inline-flex items-center gap-2 px-6 py-3.5 bg-white text-brand-700 font-bold rounded-2xl shadow-xl hover:shadow-2xl hover:bg-brand-50 transition-all duration-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Masuk Sistem
                        </a>
                        <a href="#statistik" class="inline-flex items-center gap-2 px-6 py-3.5 glass-card text-white font-semibold rounded-2xl hover:bg-white/15 transition-all duration-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Lihat Statistik
                        </a>
                    </div>
                    <!-- Quick stats -->
                    <div class="flex flex-wrap gap-6 mt-10 pt-8 border-t border-white/20">
                        <div>
                            <p class="text-2xl font-bold text-white">{{ number_format($totalPelanggan) }}+</p>
                            <p class="text-brand-300 text-xs">Pelanggan Aktif</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-white">99.2%</p>
                            <p class="text-brand-300 text-xs">Uptime Sistem</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-white">3 Desa</p>
                            <p class="text-brand-300 text-xs">Wilayah Layanan</p>
                        </div>
                    </div>
                </div>

                <!-- Right: Illustration Card -->
                <div class="relative flex justify-center lg:justify-end">
                    <div class="relative w-full max-w-sm animate-float">
                        <!-- Main Card -->
                        <div class="glass-card rounded-3xl p-6 shadow-2xl">
                            <div class="flex items-center justify-between mb-5">
                                <div>
                                    <p class="text-white/70 text-xs font-medium">Dashboard PAMSIMAS</p>
                                    <p class="text-white font-bold text-sm">Bulan Ini</p>
                                </div>
                                <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                            </div>
                            <!-- Mini chart bars -->
                            <div class="flex items-end gap-1.5 h-20 mb-4">
                                @foreach([40,65,45,80,55,90,70,85,60,95,75,88] as $h)
                                <div class="flex-1 rounded-t-sm bg-gradient-to-t from-brand-400/60 to-white/40" style="height: {{ $h }}%"></div>
                                @endforeach
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-white/10 rounded-xl p-3">
                                    <p class="text-white/60 text-xs">Tagihan Lunas</p>
                                    <p class="text-white font-bold text-lg">{{ number_format($heroTagihanLunas) }}</p>
                                    <span class="text-green-400 text-xs">▲ Bulan ini</span>
                                </div>
                                <div class="bg-white/10 rounded-xl p-3">
                                    <p class="text-white/60 text-xs">Pemakaian</p>
                                    <p class="text-white font-bold text-lg">{{ number_format($heroPemakaian) }} m³</p>
                                    <span class="text-blue-300 text-xs">▲ Bulan ini</span>
                                </div>
                            </div>
                        </div>
                        <!-- Floating badges -->
                        <div class="absolute -top-4 -left-4 glass-card rounded-2xl px-4 py-2.5 flex items-center gap-2 shadow-lg animate-float-slow">
                            <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white text-xs font-semibold">Pembayaran</p>
                                <p class="text-green-300 text-xs">Berhasil ✓</p>
                            </div>
                        </div>
                        <div class="absolute -bottom-4 -right-4 glass-card rounded-2xl px-4 py-2.5 flex items-center gap-2 shadow-lg">
                            <div class="relative w-8 h-8 ripple-effect rounded-full bg-blue-400 flex items-center justify-center">
                                <svg class="w-4 h-4 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white text-xs font-semibold">Live Monitor</p>
                                <p class="text-blue-300 text-xs">Real-time</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wave Bottom -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" class="dark:opacity-10">
                <path d="M0 40L48 45.3C96 50.7 192 61.3 288 61.3C384 61.3 480 50.7 576 42.7C672 34.7 768 29.3 864 32C960 34.7 1056 45.3 1152 48C1248 50.7 1344 45.3 1392 42.7L1440 40V80H1392C1344 80 1248 80 1152 80C1056 80 960 80 864 80C768 80 672 80 576 80C480 80 384 80 288 80C192 80 96 80 48 80H0V40Z" fill="white"/>
            </svg>
        </div>
    </section>

    <!-- ===== FITUR UTAMA ===== -->
    <section id="fitur" class="py-24 bg-white dark:bg-gray-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 rounded-full bg-brand-100 dark:bg-brand-900/40 text-brand-700 dark:text-brand-300 text-sm font-semibold mb-4">Fitur Unggulan</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white mb-4">Semua yang Anda Butuhkan</h2>
                <p class="text-gray-500 dark:text-gray-400 text-lg max-w-2xl mx-auto">Platform terpadu untuk pengelolaan air bersih — Mulai dari pencatatan meter hingga pelaporan keuangan secara otomatis dan real-time.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                $features = [
                    ['icon' => 'tagihan',    'title' => 'Manajemen Tagihan',     'desc' => 'Hitung dan kelola tagihan air secara otomatis berdasarkan pemakaian meter, dengan sistem tarif bertingkat yang fleksibel dan akurat.', 'color' => 'from-blue-500 to-brand-600',    'bg' => 'bg-blue-50 dark:bg-blue-900/20'],
                    ['icon' => 'monitoring', 'title' => 'Monitoring Pemakaian',   'desc' => 'Pantau penggunaan air secara real-time melalui grafik interaktif dan analisis data yang mudah dipahami.', 'color' => 'from-teal-500 to-emerald-600',  'bg' => 'bg-teal-50 dark:bg-teal-900/20'],
                    ['icon' => 'pengaduan',  'title' => 'Pengaduan Online',       'desc' => 'Laporkan gangguan layanan kapan saja dengan mudah, dilengkapi fitur unggah foto sebagai bukti pendukung.', 'color' => 'from-orange-500 to-red-500',    'bg' => 'bg-orange-50 dark:bg-orange-900/20'],
                    ['icon' => 'dashboard',  'title' => 'Dashboard Real-time',    'desc' => 'Akses visualisasi data lengkap melalui dashboard interaktif yang informatif untuk admin, petugas, dan pelanggan.', 'color' => 'from-purple-500 to-indigo-600', 'bg' => 'bg-purple-50 dark:bg-purple-900/20'],
                    ['icon' => 'pembayaran', 'title' => 'Pembayaran Digital',     'desc' => 'Kelola dan konfirmasi pembayaran tagihan dengan cepat, dilengkapi riwayat transaksi yang transparan dan terorganisir.', 'color' => 'from-green-500 to-teal-600',   'bg' => 'bg-green-50 dark:bg-green-900/20'],
                    ['icon' => 'laporan',    'title' => 'Laporan PDF',            'desc' => 'Generate dan unduh laporan keuangan, tagihan, serta data pemakaian dalam format PDF yang siap cetak.', 'color' => 'from-pink-500 to-rose-600',    'bg' => 'bg-pink-50 dark:bg-pink-900/20'],
                    ['icon' => 'notifikasi', 'title' => 'Notifikasi Otomatis',    'desc' => 'Dapatkan pemberitahuan otomatis untuk tagihan jatuh tempo, status pengaduan, dan informasi penting lainnya.', 'color' => 'from-yellow-500 to-amber-600', 'bg' => 'bg-yellow-50 dark:bg-yellow-900/20'],
                    ['icon' => 'multirole',  'title' => 'Multi Role',             'desc' => 'Sistem manajemen akses berbasis peran untuk Admin, Petugas, dan Pelanggan, dengan kontrol hak akses yang aman dan terstruktur.', 'color' => 'from-brand-500 to-cyan-600',  'bg' => 'bg-brand-50 dark:bg-brand-900/20'],
                ];
                @endphp

                @foreach($features as $f)
                <div class="feature-card {{ $f['bg'] }} rounded-2xl p-6 border border-transparent hover:border-brand-200 dark:hover:border-brand-700 transition-all duration-300 cursor-default group">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $f['color'] }} flex items-center justify-center mb-4 shadow-lg group-hover:scale-110 transition-transform duration-200">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @switch($f['icon'])
                                @case('tagihan')    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/> @break
                                @case('monitoring') <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/> @break
                                @case('pengaduan')  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/> @break
                                @case('dashboard')  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/> @break
                                @case('pembayaran') <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/> @break
                                @case('laporan')    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/> @break
                                @case('notifikasi') <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/> @break
                                @case('multirole')  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/> @break
                            @endswitch
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2 text-base">{{ $f['title'] }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">{{ $f['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===== STATISTIK ===== -->
    <section id="statistik" class="py-24 bg-gradient-to-br from-brand-950 via-brand-900 to-brand-800 relative overflow-hidden"
             x-intersect="animateStats()">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 30px 30px;"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 rounded-full bg-white/10 text-brand-200 text-sm font-semibold mb-4">Data Terkini</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">Angka Berbicara</h2>
                <p class="text-brand-300 text-lg">Gambaran performa sistem PAMSIMAS dalam memberikan layanan terbaik kepada masyarakat secara real-time.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
                @php
                $stats = [
                    ['icon' => 'pelanggan',     'label' => 'Total Pelanggan',    'value' => number_format($totalPelanggan),         'unit' => 'KK', 'change' => 'Pelanggan aktif terdaftar saat ini.',                                           'color' => 'from-blue-400 to-brand-500'],
                    ['icon' => 'volume',        'label' => 'Volume Air (m³)',    'value' => number_format($pemakaianBulanIni),      'unit' => 'm³', 'change' => 'Total pemakaian air bulan ' . now()->translatedFormat('F Y') . '.',             'color' => 'from-teal-400 to-emerald-500'],
                    ['icon' => 'tagihan-count', 'label' => 'Total Tagihan',      'value' => number_format($totalTagihanBulanIni),   'unit' => '',   'change' => 'Tagihan dibuat bulan ' . now()->translatedFormat('F Y') . '.',                  'color' => 'from-green-400 to-teal-500'],
                    ['icon' => 'lunas',         'label' => 'Tagihan Lunas',      'value' => $persenLunas . '%',                    'unit' => '',   'change' => 'Persentase tagihan lunas bulan ' . now()->translatedFormat('F Y') . '.',        'color' => 'from-purple-400 to-pink-500'],
                ];
                @endphp

                @foreach($stats as $s)
                <div class="stat-card glass-card rounded-2xl p-6 transition-all duration-300 cursor-default">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $s['color'] }} flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @switch($s['icon'])
                                    @case('pelanggan')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    @break
                                    @case('volume')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                    @break
                                    @case('tagihan-count')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    @break
                                    @case('lunas')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    @break
                                @endswitch
                            </svg>
                        </div>
                    </div>
                    <p class="text-white/60 text-sm mb-1">{{ $s['label'] }}</p>
                    <p class="text-white font-extrabold text-2xl counter mb-1">{{ $s['value'] }}</p>
                    <p class="text-brand-300 text-xs">{{ $s['change'] }}</p>
                </div>
                @endforeach
            </div>

            <!-- Chart -->
            <div class="glass-card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-white font-bold text-lg">Tren Pemakaian Air</h3>
                        <p class="text-brand-300 text-sm">Grafik 6 bulan terakhir</p>
                    </div>
                    <div class="flex gap-4 text-xs text-brand-300">
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-brand-400 inline-block"></span>Pemakaian (m³)</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-400 inline-block"></span>Tagihan (total)</span>
                    </div>
                </div>
                <canvas id="statsChart" height="80"></canvas>
            </div>
        </div>
    </section>

    <!-- ===== CARA KERJA ===== -->
    <section id="cara-kerja" class="py-24 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 rounded-full bg-brand-100 dark:bg-brand-900/40 text-brand-700 dark:text-brand-300 text-sm font-semibold mb-4">Alur Sistem</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white mb-4">Cara Kerja PAMSIMAS</h2>
                <p class="text-gray-500 dark:text-gray-400 text-lg max-w-xl mx-auto">Proses pengelolaan air bersih yang sederhana, transparan, dan terintegrasi — mulai dari pencatatan meter hingga pembayaran tagihan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative">
                @php
                $steps = [
                    ['no' => '01', 'icon' => 'input-meteran',    'title' => 'Input Meteran',     'desc' => 'Petugas lapangan mencatat angka meter air pelanggan setiap bulan secara akurat.', 'color' => 'bg-brand-600'],
                    ['no' => '02', 'icon' => 'hitung-tagihan',   'title' => 'Hitung Tagihan',    'desc' => 'Sistem secara otomatis menghitung besaran tagihan berdasarkan volume pemakaian air dengan skema tarif bertingkat yang telah ditetapkan.', 'color' => 'bg-teal-600'],
                    ['no' => '03', 'icon' => 'bayar-tagihan',    'title' => 'Bayar Tagihan',     'desc' => 'Pelanggan melakukan pembayaran tagihan, kemudian petugas mencatat dan memverifikasi transaksi pembayaran langsung di dalam sistem.', 'color' => 'bg-green-600'],
                    ['no' => '04', 'icon' => 'monitoring-admin', 'title' => 'Monitoring Admin',  'desc' => 'Admin memantau seluruh aktivitas sistem, mulai dari data pemakaian, status pembayaran, laporan keuangan, hingga performa layanan secara real-time.', 'color' => 'bg-purple-600'],
                ];
                @endphp

                @foreach($steps as $step)
                <div class="relative text-center">
                    @if(!$loop->last)
                    <div class="hidden lg:block absolute top-8 left-3/4 w-1/2 h-0.5 bg-gradient-to-r from-brand-300 to-brand-100 dark:from-brand-700 dark:to-brand-900 z-0"></div>
                    @endif
                    <div class="relative z-10 inline-flex flex-col items-center">
                        <div class="w-16 h-16 rounded-2xl {{ $step['color'] }} flex items-center justify-center shadow-xl mb-4 relative">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @switch($step['icon'])
                                    @case('input-meteran')    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/> @break
                                    @case('hitung-tagihan')   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/> @break
                                    @case('bayar-tagihan')    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/> @break
                                    @case('monitoring-admin') <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/> @break
                                @endswitch
                            </svg>
                            <span class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-white dark:bg-gray-900 border-2 border-current text-xs font-bold text-gray-800 dark:text-white flex items-center justify-center shadow">{{ $loop->iteration }}</span>
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-2">{{ $step['title'] }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed max-w-[200px]">{{ $step['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===== TESTIMONI ===== -->
    <section id="testimoni" class="py-24 bg-white dark:bg-gray-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-1.5 rounded-full bg-brand-100 dark:bg-brand-900/40 text-brand-700 dark:text-brand-300 text-sm font-semibold mb-4">Testimoni</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white mb-4">Apa Kata Mereka</h2>
                <p class="text-gray-500 dark:text-gray-400 text-lg">Pengalaman nyata dari pengguna sistem PAMSIMAS dalam mendukung pelayanan yang lebih mudah, cepat, dan transparan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                $testimonials = [
                    ['name' => 'Budi Santoso', 'role' => 'Pelanggan — RT 03', 'avatar' => 'BS', 'color' => 'from-blue-500 to-brand-600',  'text' => 'Sejak menggunakan sistem ini, saya tidak perlu lagi repot datang ke kantor hanya untuk mengecek tagihan air. Sekarang semuanya bisa diakses langsung dari HP kapan saja dan di mana saja. Informasi tagihan juga lebih jelas, dan proses pembayaran jadi jauh lebih praktis. Setelah bayar, konfirmasi langsung masuk, jadi saya tidak perlu khawatir lagi soal keterlambatan atau kesalahan pencatatan.', 'rating' => 5],
                    ['name' => 'Siti Rahayu',  'role' => 'Ketua RT 07',       'avatar' => 'SR', 'color' => 'from-pink-500 to-rose-600',    'text' => 'Sebagai ketua RT, sistem ini sangat membantu dalam memantau kondisi pelayanan air di lingkungan saya. Saya bisa melihat laporan dengan lebih transparan dan cepat tanpa harus menunggu lama. Selain itu, pengaduan dari warga juga bisa langsung masuk ke sistem dan diproses lebih cepat. Adanya notifikasi membuat koordinasi menjadi lebih efektif dan pelayanan ke warga jadi lebih optimal.', 'rating' => 5],
                    ['name' => 'Ahmad Fauzi',  'role' => 'Petugas Lapangan',  'avatar' => 'AF', 'color' => 'from-green-500 to-teal-600',  'text' => 'Dari sisi petugas lapangan, sistem ini sangat mempermudah pekerjaan kami dalam mencatat angka meter air pelanggan. Proses pencatatan menjadi lebih cepat, akurat, dan tidak lagi bergantung pada pencatatan manual yang rawan kesalahan. Data yang masuk juga langsung tersimpan di sistem, sehingga lebih aman dan mudah diakses kembali saat dibutuhkan untuk pelaporan maupun pengecekan.', 'rating' => 5],
                ];
                @endphp

                @foreach($testimonials as $t)
                <div class="testi-card bg-gray-50 dark:bg-gray-900 rounded-2xl p-6 border border-gray-100 dark:border-gray-800 relative">
                    <!-- Quote icon -->
                    <div class="absolute top-4 right-4 text-brand-100 dark:text-brand-900">
                        <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                        </svg>
                    </div>
                    <!-- Stars -->
                    <div class="flex gap-1 mb-4">
                        @for($i = 0; $i < $t['rating']; $i++)
                        <svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed mb-6 italic">"{{ $t['text'] }}"</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $t['color'] }} flex items-center justify-center text-white font-bold text-sm">{{ $t['avatar'] }}</div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $t['name'] }}</p>
                            <p class="text-gray-400 text-xs">{{ $t['role'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ===== CTA SECTION ===== -->
    <section class="py-24 relative overflow-hidden hero-gradient">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 30px 30px;"></div>
        <div class="absolute top-10 right-10 w-80 h-80 rounded-full bg-white/5 animate-float-slow"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <div class="glass-card rounded-3xl p-12">
                <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">Siap Memulai?</h2>
                <p class="text-brand-200 text-lg mb-8 max-w-xl mx-auto">Kelola layanan air bersih Anda dengan lebih mudah, cepat, dan transparan melalui sistem PAMSIMAS yang terintegrasi. Mulai dari pencatatan meter, perhitungan tagihan, hingga laporan keuangan — semuanya dapat diakses dalam satu platform yang modern dan efisien.</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="/login" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-brand-700 font-bold rounded-2xl shadow-xl hover:shadow-2xl hover:bg-brand-50 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Login ke Sistem
                    </a>
                    <a href="#fitur" class="inline-flex items-center gap-2 px-8 py-4 glass-card text-white font-semibold rounded-2xl hover:bg-white/20 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pelajari Lebih Lanjut
                    </a>
                </div>
                <!-- Role badges -->
                <div class="flex flex-wrap justify-center gap-3 mt-8 pt-6 border-t border-white/20">
                    <span class="px-4 py-1.5 bg-white/10 text-white/80 rounded-full text-sm font-medium">👤 Admin</span>
                    <span class="px-4 py-1.5 bg-white/10 text-white/80 rounded-full text-sm font-medium">🔧 Petugas</span>
                    <span class="px-4 py-1.5 bg-white/10 text-white/80 rounded-full text-sm font-medium">🏠 Pelanggan</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="bg-gray-950 dark:bg-black py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Brand -->
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-bold">PAMSIMAS</p>
                            <p class="text-gray-500 text-xs">Program Penyediaan dan Pengelolaan Air Minum Berbasis Masyarakat</p>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed max-w-sm">Platform digital terpadu yang dirancang untuk mendukung pengelolaan sistem air minum berbasis masyarakat secara efisien, transparan, dan akuntabel. Dari pencatatan konsumsi air, pemantauan jaringan distribusi, hingga pelaporan keuangan — semua terintegrasi dalam satu sistem yang mudah diakses oleh seluruh pemangku kepentingan.</p>
                </div>
                <!-- Links -->
                <div>
                    <p class="text-white font-semibold text-sm mb-3">Tentang Sistem</p>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="/login" class="hover:text-brand-400 transition-colors">Masuk ke akun</a></li>
                        <li><a href="#fitur" class="hover:text-brand-400 transition-colors">Layanan & Fitur</a></li>
                        <li><a href="#cara-kerja" class="hover:text-brand-400 transition-colors">Pengaduan dan Cara Kerja</a></li>
                    </ul>
                </div>
                <div>
                    <p class="text-white font-semibold text-sm mb-3">Akses</p>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><span class="text-gray-500">Admin Dashboard</span></li>
                        <li><span class="text-gray-500">Petugas Panel</span></li>
                        <li><span class="text-gray-500">Pelanggan Portal</span></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-gray-500 text-sm">© {{ date('Y') }} Sistem Informasi PAMSIMAS – Penyediaan Air Minum Berbasis Masyarakat.</p>
                <p class="text-gray-600 text-xs">Dibangun dengan Laravel & Tailwind CSS</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('statsChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'Pemakaian Air (m³)',
                            data: @json($chartPemakaian),
                            backgroundColor: 'rgba(96,180,247,0.7)',
                            borderColor: '#60b4f7',
                            borderWidth: 2,
                            borderRadius: 8,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Total Tagihan',
                            data: @json($chartTagihan),
                            backgroundColor: 'rgba(52,211,153,0.7)',
                            borderColor: '#34d399',
                            borderWidth: 2,
                            borderRadius: 8,
                            type: 'line',
                            yAxisID: 'y1',
                            tension: 0.4,
                            fill: false,
                            pointRadius: 5,
                            pointBackgroundColor: '#34d399',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { labels: { color: 'rgba(255,255,255,0.7)', font: { size: 12 } } },
                        tooltip: { backgroundColor: 'rgba(23,42,83,0.9)', titleColor: '#fff', bodyColor: '#93c5fd', cornerRadius: 10, padding: 12 }
                    },
                    scales: {
                        x: { ticks: { color: 'rgba(255,255,255,0.5)' }, grid: { color: 'rgba(255,255,255,0.05)' } },
                        y:  { type: 'linear', display: true, position: 'left',  ticks: { color: 'rgba(255,255,255,0.5)' },       grid: { color: 'rgba(255,255,255,0.05)' } },
                        y1: { type: 'linear', display: true, position: 'right', ticks: { color: 'rgba(52,211,153,0.7)' },         grid: { drawOnChartArea: false } }
                    }
                }
            });
        });
    </script>
</body>
</html>