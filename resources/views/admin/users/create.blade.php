{{-- resources/views/admin/users/create.blade.php --}}
@extends('layouts.app')
@section('title','Tambah Pengguna')
@section('page_title','Tambah Pengguna Baru')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
</div>

<div class="max-w-xl">
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-6">
        <h2 class="font-bold text-gray-800 dark:text-white text-lg mb-5">Data Pengguna</h2>
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf
            @include('admin.users._form', ['user' => null])
            <div class="pt-2">
                <button type="submit" class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
@endsection