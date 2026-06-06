@extends('layouts.app')
@section('title','Tambah Petugas')
@section('page_title','Tambah Petugas Baru')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.petugas.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Kembali
    </a>
</div>

<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6">

        {{-- Foto Preview --}}
        <div class="flex flex-col items-center mb-6">
            <div class="relative">
                <img id="foto-preview" src="https://ui-avatars.com/api/?name=Petugas&background=3b93f2&color=fff&size=128"
                    class="w-24 h-24 rounded-2xl object-cover border-4 border-brand-100 shadow">
                <label for="foto" class="absolute -bottom-2 -right-2 w-8 h-8 bg-brand-500 hover:bg-brand-600 rounded-full flex items-center justify-center cursor-pointer shadow-lg transition-colors">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </label>
            </div>
            <p class="text-xs text-gray-400 mt-3">Klik ikon kamera untuk upload foto</p>
        </div>

        @if($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-2xl text-sm text-red-700">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.petugas.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="file" id="foto" name="foto" accept="image/*" class="hidden">

            <div class="grid grid-cols-2 gap-4">

                {{-- Nama --}}
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nama Petugas <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_petugas" value="{{ old('nama_petugas') }}" required
                        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    @error('nama_petugas')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- NIK --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">NIK (16 digit)</label>
                    <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" placeholder="Nomor Induk Kependudukan"
                        inputmode="numeric"
                        oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    <p class="mt-1 text-xs text-gray-400">Harus tepat 16 digit angka</p>
                    @error('nik')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">NIP</label>
                    <input type="text" name="nip" value="{{ old('nip') }}"
                        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    @error('nip')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div> --}}

                {{-- Jabatan --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Jabatan</label>
                    <select name="jabatan"
                        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach($jabatanList as $j)
                        <option value="{{ $j }}" {{ old('jabatan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- No HP --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">No. HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                        inputmode="numeric"
                        oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                        maxlength="15"
                        placeholder="08xxxxxxxxxx"
                        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    <p class="mt-1 text-xs text-gray-400">Minimal 10 digit, angka saja</p>
                </div>

                {{-- Tanggal Lahir --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                </div>

                {{-- TMT --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">TMT (Terhitung Mulai Tanggal)</label>
                    <input type="date" name="tmt" value="{{ old('tmt') }}"
                        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    <p class="text-xs text-gray-400 mt-1">Tanggal pertama kali bergabung</p>
                </div>

                {{-- Alamat --}}
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Alamat</label>
                    <textarea name="alamat" rows="2"
                        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none transition-all">{{ old('alamat') }}</textarea>
                </div>

                {{-- Akun Login --}}
                <div class="col-span-2">
                    <div class="border-t border-gray-100 dark:border-gray-800 pt-4 mb-2">
                        <p class="text-sm font-bold text-gray-700 dark:text-gray-300">🔐 Akun Login</p>
                        <p class="text-xs text-gray-400 mt-0.5">Email dan password untuk petugas masuk ke sistem</p>
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required autocomplete="off"
                        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Password <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="password" name="password" id="inp-password" required autocomplete="new-password"
                            oninput="cekPassword(this.value)"
                            placeholder="Min. 8 karakter"
                            class="w-full py-2.5 px-4 pr-10 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    {{-- Strength bar --}}
                    <div class="mt-2">
                        <div class="flex gap-1 mb-1">
                            <div id="bar1" class="h-1 flex-1 rounded-full bg-gray-200 transition-all"></div>
                            <div id="bar2" class="h-1 flex-1 rounded-full bg-gray-200 transition-all"></div>
                            <div id="bar3" class="h-1 flex-1 rounded-full bg-gray-200 transition-all"></div>
                            <div id="bar4" class="h-1 flex-1 rounded-full bg-gray-200 transition-all"></div>
                        </div>
                        <p id="rule-length"  class="text-xs text-gray-400">✗ Minimal 8 karakter</p>
                        <p id="rule-huruf"   class="text-xs text-gray-400">✗ Mengandung huruf</p>
                        <p id="rule-angka"   class="text-xs text-gray-400">✗ Mengandung angka</p>
                        <p id="rule-special" class="text-xs text-gray-400">✗ Mengandung karakter spesial (!@#$)</p>
                    </div>
                    @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow transition-all">Tambah Petugas</button>
                <a href="{{ route('admin.petugas.index') }}" class="px-5 py-3 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all text-sm">Batal</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('foto').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('foto-preview').src = e.target.result;
        reader.readAsDataURL(file);
    }
});

function cekPassword(val) {
    const rules = {
        length:  val.length >= 8,
        huruf:   /[a-zA-Z]/.test(val),
        angka:   /[0-9]/.test(val),
        special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(val)
    };
    const labels = {
        length:  '✗ Minimal 8 karakter',
        huruf:   '✗ Mengandung huruf',
        angka:   '✗ Mengandung angka',
        special: '✗ Mengandung karakter spesial (!@#$)'
    };
    Object.keys(rules).forEach(k => {
        const el = document.getElementById('rule-' + k);
        el.className = 'text-xs ' + (rules[k] ? 'text-green-500' : 'text-gray-400');
        el.textContent = (rules[k] ? '✓' : '✗') + labels[k].substring(1);
    });
    const skor = Object.values(rules).filter(Boolean).length;
    const colors = ['bg-gray-200','bg-red-400','bg-orange-400','bg-yellow-400','bg-green-500'];
    for (let i = 1; i <= 4; i++) {
        document.getElementById('bar' + i).className = 'h-1 flex-1 rounded-full transition-all ' + (i <= skor ? colors[skor] : 'bg-gray-200');
    }
}

function togglePassword() {
    const inp = document.getElementById('inp-password');
    inp.type = inp.type === 'password' ? 'text' : 'password';
}
</script>
@endpush
@endsection