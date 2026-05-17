<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: false, showPass: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — PAMSIMAS.ID</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config={darkMode:'class',theme:{extend:{colors:{brand:{50:'#eff8ff',100:'#dbeffe',200:'#bfe3fd',300:'#93d0fb',400:'#60b4f7',500:'#3b93f2',600:'#2574e6',700:'#1d5fd4',800:'#1e4dab',900:'#1e4287',950:'#172a53'}},fontFamily:{display:['"Plus Jakarta Sans"','sans-serif'],body:['"DM Sans"','sans-serif']}}}}
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        *{font-family:'DM Sans',sans-serif;}
        h1,h2,h3{font-family:'Plus Jakarta Sans',sans-serif;}
        .login-bg{background:linear-gradient(135deg,#172a53 0%,#1e4287 30%,#2574e6 70%,#60b4f7 100%);}
        .glass{background:rgba(255,255,255,0.08);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,0.15);}
        @keyframes float{0%,100%{transform:translateY(0);}50%{transform:translateY(-18px);}}
        .float{animation:float 7s ease-in-out infinite;}
        .float-2{animation:float 5.5s ease-in-out infinite 1.5s;}
    </style>
</head>
<body class="min-h-screen login-bg flex items-center justify-center p-4 relative overflow-y-auto">
    {{-- BG decorations --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-blue-400/10 float"></div>
        <div class="absolute -bottom-32 -left-32 w-80 h-80 rounded-full bg-white/5 float-2"></div>
        <div class="absolute top-1/3 right-1/4 w-32 h-32 rounded-full bg-blue-300/8 float"></div>
        <div class="absolute inset-0 opacity-5" style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:32px 32px;"></div>
    </div>

    {{-- Dark toggle --}}
    <button @click="darkMode = !darkMode"
        class="fixed top-4 right-4 z-50 p-2 rounded-xl glass text-white hover:bg-white/20 transition-all">
        <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
        </svg>
        <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
    </button>

    <div class="w-full max-w-md relative z-10">

        {{-- Logo --}}
        <div class="text-center mb-7">
            <a href="/" class="inline-flex flex-col items-center group">
                <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center mb-4 shadow-2xl group-hover:bg-white/30 transition-all">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight">PAMSIMAS</h1>
                <p class="text-blue-200 text-sm mt-1">Sistem Air Minum Berbasis Masyarakat</p>
            </a>
        </div>

        {{-- Login Card --}}
        <div class="glass rounded-3xl p-7 shadow-2xl">
            <h2 class="text-xl font-bold text-white mb-1">Selamat Datang</h2>
            <p class="text-blue-200 text-sm mb-5">Masukkan kredensial Anda untuk melanjutkan</p>

            {{-- Success Message --}}
            @if(session('success'))
            <div class="mb-4 p-4 rounded-xl bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 text-sm flex items-start gap-2">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>{{ session('success') }}</p>
            </div>
            @endif

            {{-- Error Messages --}}
            @if($errors->any())
            <div class="mb-4 p-4 rounded-xl bg-red-500/20 border border-red-400/30 text-red-200 text-sm">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        @foreach($errors->all() as $e)
                        <p>{{ $e }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                {{-- Email --}}
                <div>
                    <label class="block text-blue-200 text-sm font-medium mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-blue-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            autocomplete="email"
                            class="w-full pl-11 pr-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all"
                            placeholder="email@contoh.com">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-blue-200 text-sm font-medium mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-blue-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input :type="showPass ? 'text' : 'password'" name="password" required
                            autocomplete="current-password"
                            class="w-full pl-11 pr-12 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all"
                            placeholder="••••••••">
                        <button type="button" @click="showPass = !showPass"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-blue-300 hover:text-white transition-colors">
                            <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-white/30 bg-white/10 text-blue-500 focus:ring-blue-400">
                        <span class="text-blue-200 text-sm">Ingat saya</span>
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full py-3.5 bg-white text-blue-800 font-bold rounded-xl shadow-xl hover:bg-blue-50 hover:shadow-2xl transition-all duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Masuk ke Sistem
                </button>
            </form>

            {{-- Demo Credentials --}}
            <div class="mt-5 pt-5 border-t border-white/10">
                <p class="text-blue-300 text-xs text-center mb-3 font-medium tracking-wide uppercase">Demo Akun</p>
                <div class="grid grid-cols-3 gap-2" x-data>
                    <button @click="document.querySelector('input[name=email]').value='admin@pamsimas.id';document.querySelector('input[name=password]').value='password'"
                        class="py-2.5 px-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition-all border border-white/10 hover:border-white/25">
                        👤 Admin
                    </button>
                    <button @click="document.querySelector('input[name=email]').value='petugas@pamsimas.id';document.querySelector('input[name=password]').value='password'"
                        class="py-2.5 px-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition-all border border-white/10 hover:border-white/25">
                        🔧 Petugas
                    </button>
                    <button @click="document.querySelector('input[name=email]').value='pelanggan@pamsimas.id';document.querySelector('input[name=password]').value='password'"
                        class="py-2.5 px-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition-all border border-white/10 hover:border-white/25">
                        🏠 Pelanggan
                    </button>
                </div>
            </div>

            {{-- Register Link --}}
            {{-- <div class="mt-5 pt-5 border-t border-white/10 text-center">
                <p class="text-blue-200 text-sm mb-3">Belum punya akun?</p>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('register.form', 'pelanggan') }}"
                        class="py-2.5 text-center text-xs font-semibold text-white glass rounded-xl hover:bg-white/15 transition-all border border-white/15">
                        🏠 Daftar Pelanggan
                    </a>
                    <a href="{{ route('register.form', 'petugas') }}"
                        class="py-2.5 text-center text-xs font-semibold text-white glass rounded-xl hover:bg-white/15 transition-all border border-white/15">
                        🔧 Daftar Petugas
                    </a>
                </div>
                <p class="text-blue-300 text-xs mt-3">
                    ℹ️ Pendaftaran memerlukan persetujuan admin
                </p>
            </div>
        </div> --}}

        {{-- Back link --}}
        <p class="text-center mt-5">
            <a href="/" class="text-blue-200 text-sm hover:text-white transition-colors inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Beranda
            </a>
        </p>
    </div>
</body>
</html>
