{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.app')
@section('title','Edit Pengguna')
@section('page_title','Edit Pengguna')
@section('page_subtitle', $user->name)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
</div>

<div class="max-w-xl">
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6">
        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100 dark:border-gray-800">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-bold">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <p class="font-bold text-gray-800 dark:text-white">{{ $user->name }}</p>
                <p class="text-xs text-gray-400">{{ $user->email }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
            @csrf @method('PUT')
            @include('admin.users._form', ['user' => $user])
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow transition-all">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-5 py-3 border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all text-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
