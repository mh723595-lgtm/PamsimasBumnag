{{-- resources/views/petugas/pengaduan/index.blade.php --}}
@extends('layouts.app')
@section('title','Pengaduan')
@section('page_title','Daftar Pengaduan')
@section('page_subtitle','Tangani pengaduan dari pelanggan')

@section('content')
<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/60">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. / Judul</th>
                    <th class="text-left px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Prioritas</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($pengaduan as $p)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors {{ $p->status === 'baru' ? 'bg-blue-50/30' : '' }}">
                    <td class="px-5 py-3.5">
                        <p class="font-mono text-xs text-brand-600 dark:text-brand-400 mb-0.5">{{ $p->nomor_pengaduan }}</p>
                        <p class="font-semibold text-gray-800 dark:text-gray-200 text-sm">{{ Str::limit($p->judul, 40) }}</p>
                        <p class="text-xs text-gray-400">{{ $p->created_at->diffForHumans() }}</p>
                    </td>
                    <td class="px-3 py-3.5">
                        <p class="font-medium text-gray-700 dark:text-gray-300 text-sm">{{ $p->pelanggan->nama_pelanggan }}</p>
                        <p class="text-xs text-gray-400">{{ $p->pelanggan->nomor_pelanggan }}</p>
                    </td>
                    <td class="px-3 py-3.5 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 capitalize">{{ $p->jenis }}</span>
                    </td>
                    <td class="px-3 py-3.5 text-center text-lg">
                        {{ $p->prioritas === 'tinggi' ? '🔴' : ($p->prioritas === 'sedang' ? '🟡' : '🟢') }}
                    </td>
                    <td class="px-3 py-3.5 text-center">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $p->statusBadge() }}">
                            {{ ucfirst($p->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <a href="{{ route('petugas.pengaduan.show', $p) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-brand-600 bg-brand-50 dark:bg-brand-900/30 hover:bg-brand-100 dark:hover:bg-brand-900/50 rounded-lg transition-all">
                            Lihat Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-16 text-center text-gray-400">Tidak ada pengaduan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pengaduan->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
        {{ $pengaduan->links() }}
    </div>
    @endif
</div>
@endsection
