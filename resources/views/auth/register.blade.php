<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: false, showPass: false, showPass2: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar {{ ucfirst($role) }} — PAMSIMAS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={darkMode:'class',theme:{extend:{colors:{brand:{50:'#eff8ff',100:'#dbeffe',200:'#bfe3fd',300:'#93d0fb',400:'#60b4f7',500:'#3b93f2',600:'#2574e6',700:'#1d5fd4',800:'#1e4dab',900:'#1e4287',950:'#172a53'}},fontFamily:{display:['"Plus Jakarta Sans"','sans-serif'],body:['"DM Sans"','sans-serif']}}}}</script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        *{font-family:'DM Sans',sans-serif;}
        h1,h2,h3{font-family:'Plus Jakarta Sans',sans-serif;}
        .reg-bg{background:linear-gradient(135deg,#172a53 0%,#1e4287 40%,#2574e6 80%,#60b4f7 100%);}
        .glass{background:rgba(255,255,255,0.09);backdrop-filter:blur(16px);border:1px solid rgba(255,255,255,0.15);}
    </style>
</head>
<body class="min-h-screen reg-bg flex items-start justify-center p-4 py-8 relative">

    <div class="absolute inset-0 opacity-5 pointer-events-none" style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:32px 32px;"></div>

    <div class="w-full max-w-lg relative z-10">

        {{-- Header --}}
        <div class="text-center mb-6">
            <a href="/" class="inline-flex flex-col items-center group">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center mb-3 shadow-2xl group-hover:bg-white/30 transition-all">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-white">PAMSIMAS</h1>
                <p class="text-blue-200 text-sm mt-0.5">Pendaftaran Akun Baru</p>
            </a>
        </div>

        {{-- Role Tabs --}}
        <div class="glass rounded-2xl p-1.5 flex gap-1.5 mb-4">
            <a href="{{ route('register.form', 'pelanggan') }}"
                class="flex-1 py-2.5 text-center text-sm font-semibold rounded-xl transition-all
                {{ $role === 'pelanggan' ? 'bg-white text-brand-700 shadow' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                🏠 Pelanggan
            </a>
            <a href="{{ route('register.form', 'petugas') }}"
                class="flex-1 py-2.5 text-center text-sm font-semibold rounded-xl transition-all
                {{ $role === 'petugas' ? 'bg-white text-brand-700 shadow' : 'text-white/70 hover:text-white hover:bg-white/10' }}">
                🔧 Petugas
            </a>
        </div>

        {{-- Form Card --}}
        <div class="glass rounded-3xl p-6 shadow-2xl">
            <h2 class="text-lg font-bold text-white mb-1">
                Daftar sebagai {{ $role === 'pelanggan' ? 'Pelanggan' : 'Petugas' }}
            </h2>
            <p class="text-blue-200 text-sm mb-5">
                {{ $role === 'pelanggan' ? 'Daftarkan diri untuk mengakses layanan air PAMSIMAS' : 'Daftar sebagai petugas lapangan PAMSIMAS' }}
            </p>

            @if(session('success'))
            <div class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 text-sm">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-red-500/20 border border-red-400/30 text-red-200 text-sm">
                <p class="font-semibold mb-1">Perbaiki kesalahan berikut:</p>
                <ul class="list-disc list-inside space-y-0.5 text-xs">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="role" value="{{ $role }}">

                {{-- Nama --}}
                <div>
                    <label class="block text-blue-200 text-xs font-semibold mb-1.5">Nama Lengkap *</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required
                        placeholder="Nama lengkap sesuai KTP"
                        class="w-full py-2.5 px-4 text-sm bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-brand-400 transition-all">
                </div>

                {{-- Email & Password --}}
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-blue-200 text-xs font-semibold mb-1.5">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            placeholder="email@contoh.com"
                            class="w-full py-2.5 px-4 text-sm bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-brand-400 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-blue-200 text-xs font-semibold mb-1.5">Password *</label>
                        <div class="relative">
                            <input :type="showPass ? 'text' : 'password'" name="password" required
                                placeholder="Min 6 karakter"
                                class="w-full py-2.5 pl-4 pr-9 text-sm bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-brand-400 transition-all">
                            <button type="button" @click="showPass=!showPass"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-blue-300 hover:text-white">
                                <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-blue-200 text-xs font-semibold mb-1.5">Konfirmasi *</label>
                        <div class="relative">
                            <input :type="showPass2 ? 'text' : 'password'" name="password_confirmation" required
                                placeholder="Ulangi password"
                                class="w-full py-2.5 pl-4 pr-9 text-sm bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-brand-400 transition-all">
                            <button type="button" @click="showPass2=!showPass2"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-blue-300 hover:text-white">
                                <svg x-show="!showPass2" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPass2" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- No HP --}}
                <div>
                    <label class="block text-blue-200 text-xs font-semibold mb-1.5">Nomor HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                        placeholder="08xxxxxxxxxx"
                        class="w-full py-2.5 px-4 text-sm bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-brand-400 transition-all">
                </div>

                {{-- Alamat --}}
                <div>
                    <label class="block text-blue-200 text-xs font-semibold mb-1.5">Alamat Lengkap *</label>
                    <textarea name="alamat" rows="2" required
                        placeholder="Jl. nama jalan, RT/RW, nomor rumah..."
                        class="w-full py-2.5 px-4 text-sm bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-brand-400 resize-none transition-all">{{ old('alamat') }}</textarea>
                </div>

                @if($role === 'pelanggan')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-blue-200 text-xs font-semibold mb-1.5">Desa/Kelurahan</label>
                        <input type="text" name="desa" value="{{ old('desa') }}"
                            class="w-full py-2.5 px-4 text-sm bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-brand-400 transition-all">
                    </div>
                    <div>
                        <label class="block text-blue-200 text-xs font-semibold mb-1.5">Kecamatan</label>
                        <input type="text" name="kecamatan" value="{{ old('kecamatan') }}"
                            class="w-full py-2.5 px-4 text-sm bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-brand-400 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-blue-200 text-xs font-semibold mb-1.5">No. KTP</label>
                    <input type="text" name="no_ktp" value="{{ old('no_ktp') }}"
                        placeholder="16 digit NIK"
                        class="w-full py-2.5 px-4 text-sm bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-brand-400 transition-all">
                </div>
                @endif

                @if($role === 'petugas')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-blue-200 text-xs font-semibold mb-1.5">NIP (jika ada)</label>
                        <input type="text" name="nip" value="{{ old('nip') }}"
                            class="w-full py-2.5 px-4 text-sm bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-brand-400 transition-all">
                    </div>
                    <div>
                        <label class="block text-blue-200 text-xs font-semibold mb-1.5">Jabatan</label>
                        <input type="text" name="jabatan" value="{{ old('jabatan') }}"
                            placeholder="Teknisi Lapangan"
                            class="w-full py-2.5 px-4 text-sm bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-300 focus:outline-none focus:ring-2 focus:ring-brand-400 transition-all">
                    </div>
                </div>
                @endif

                {{-- Info approval --}}
                <div class="p-3.5 bg-amber-500/15 border border-amber-400/30 rounded-xl">
                    <p class="text-amber-200 text-xs leading-relaxed">
                        ⚠️ <strong>Perlu Persetujuan Admin:</strong> Akun Anda akan aktif setelah diverifikasi oleh administrator PAMSIMAS.
                        Proses verifikasi biasanya memakan waktu 1-2 hari kerja.
                    </p>
                </div>

                <button type="submit"
                    class="w-full py-3.5 bg-white text-brand-700 font-bold rounded-xl shadow-xl hover:bg-brand-50 hover:shadow-2xl transition-all duration-200 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Daftar Sekarang
                </button>
            </form>

            <p class="text-center text-blue-200 text-sm mt-5">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-white font-semibold hover:underline">Login di sini</a>
            </p>
        </div>

        <p class="text-center mt-4">
            <a href="/" class="text-blue-300 text-sm hover:text-white transition-colors inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Beranda
            </a>
        </p>
    </div>
</body>
</html>
