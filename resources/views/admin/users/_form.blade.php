{{-- resources/views/admin/users/_form.blade.php --}}
@php $editing = isset($user) && $user !== null; @endphp

<div>
    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
    <input type="text" name="name" value="{{ old('name', $editing ? $user->name : '') }}" required
        placeholder="Nama lengkap pengguna"
        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email <span class="text-red-500">*</span></label>
    <input type="email" name="email" value="{{ old('email', $editing ? $user->email : '') }}" required
        placeholder="email@contoh.com"
        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Role <span class="text-red-500">*</span></label>
    <div class="grid grid-cols-3 gap-2">
        @foreach(['admin'=>['👤','Admin','purple'],'petugas'=>['🔧','Petugas','blue'],'pelanggan'=>['🏠','Pelanggan','emerald']] as $val=>[$ico,$label,$color])
        <label class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 cursor-pointer text-xs font-semibold transition-all
            {{ old('role', $editing ? $user->role : '') === $val ? "border-{$color}-500 bg-{$color}-50 dark:bg-{$color}-900/20 text-{$color}-700" : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-gray-300' }}">
            <input type="radio" name="role" value="{{ $val }}"
                {{ old('role', $editing ? $user->role : '') === $val ? 'checked' : '' }}
                class="sr-only">
            <span class="text-xl">{{ $ico }}</span>
            <span>{{ $label }}</span>
        </label>
        @endforeach
    </div>
    @error('role')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
        Password {{ $editing ? '<span class="text-gray-400 font-normal text-xs">(kosongkan jika tidak diubah)</span>' : '<span class="text-red-500">*</span>' }}
    </label>
    <input type="password" name="password" {{ $editing ? '' : 'required' }}
        placeholder="{{ $editing ? 'Biarkan kosong jika tidak diubah' : 'Minimal 6 karakter' }}"
        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
    @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
</div>

<div>
    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Konfirmasi Password</label>
    <input type="password" name="password_confirmation"
        placeholder="Ulangi password"
        class="w-full py-2.5 px-4 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
</div>

@if($editing)
<div>
    <label class="flex items-center gap-3 cursor-pointer">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
            class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Akun Aktif</span>
    </label>
</div>
@endif
