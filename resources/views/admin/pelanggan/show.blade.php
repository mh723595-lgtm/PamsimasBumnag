{{-- resources/views/admin/pelanggan/show.blade.php --}}
@extends('layouts.app')
@section('title','Detail Pelanggan')
@section('page_title','Detail Pelanggan')
@section('page_subtitle',$pelanggan->nomor_pelanggan)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.pelanggan.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Kembali
    </a>
</div>

@if(session('success'))
<div class="mb-4 flex items-center gap-3 px-4 py-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-xl text-green-800 dark:text-green-300 text-sm">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Kolom kiri --}}
    <div class="space-y-4">

        {{-- Card info pelanggan --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
            <div class="flex items-center gap-4 mb-5">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-700 flex items-center justify-center text-white font-bold text-xl">
                    {{ strtoupper(substr($pelanggan->nama_pelanggan,0,2)) }}
                </div>
                <div>
                    <p class="font-bold text-gray-800 dark:text-white text-lg">{{ $pelanggan->nama_pelanggan }}</p>
                    <p class="font-mono text-xs text-brand-600 dark:text-brand-400">{{ $pelanggan->nomor_pelanggan }}</p>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold mt-1 capitalize
                        {{ $pelanggan->status==='aktif' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' }}">
                        {{ $pelanggan->status }}
                    </span>
                </div>
            </div>
            <div class="space-y-0 text-sm">
                @foreach([
                    ['Alamat',       $pelanggan->alamat],
                    ['RT/RW',        $pelanggan->rt_rw ?? '-'],
                    ['Desa',         $pelanggan->desa ?? '-'],
                    ['No. HP',       $pelanggan->no_hp ?? '-'],
                    ['Meteran Awal', number_format($pelanggan->meteran_awal).' m³'],
                    ['Tgl Daftar',   $pelanggan->tanggal_daftar->format('d/m/Y')],
                    ['Email',        $pelanggan->user->email],
                ] as [$k,$v])
                <div class="flex justify-between gap-3 py-2 border-b border-gray-50 dark:border-gray-800 last:border-0">
                    <span class="text-gray-400 text-xs flex-shrink-0">{{ $k }}</span>
                    <span class="text-gray-700 dark:text-gray-300 text-right text-xs font-medium">{{ $v }}</span>
                </div>
                @endforeach
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.pelanggan.edit', $pelanggan) }}"
                    class="block w-full py-2 text-center text-sm font-semibold bg-brand-600 hover:bg-brand-700 text-white rounded-xl transition-all">Edit</a>
            </div>
        </div>

        {{-- Card Petugas --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-bold text-gray-700 dark:text-gray-200">Petugas Assigned</h4>
                <button onclick="openAssignModal()"
                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-lg bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 hover:bg-teal-100 transition-all">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Ubah
                </button>
            </div>
            @if($pelanggan->petugas)
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                    {{ strtoupper(substr($pelanggan->petugas->nama_petugas,0,2)) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $pelanggan->petugas->nama_petugas }}</p>
                    <p class="text-xs text-gray-400">{{ $pelanggan->petugas->no_hp ?? '-' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.pelanggan.hapus-petugas', $pelanggan) }}" class="mt-3"
                onsubmit="return confirm('Hapus petugas dari pelanggan ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full py-1.5 text-xs font-semibold text-red-600 border border-red-200 dark:border-red-800 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                    Hapus Petugas
                </button>
            </form>
            @else
            <div class="flex flex-col items-center py-4 text-center">
                <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <p class="text-xs text-gray-400">Belum ada petugas</p>
                <button onclick="openAssignModal()" class="mt-2 px-3 py-1.5 text-xs font-semibold rounded-lg bg-teal-600 hover:bg-teal-700 text-white transition-all">
                    Assign Sekarang
                </button>
            </div>
            @endif
        </div>
    </div>

    {{-- Kolom kanan: tab --}}
    <div class="lg:col-span-2">
        <div class="flex gap-1 mb-3 bg-gray-100 dark:bg-gray-800 p-1 rounded-xl w-fit">
            <button onclick="switchTab('tagihan')" id="tab-tagihan"
                class="tab-btn px-4 py-2 text-sm font-semibold rounded-lg transition-all bg-white dark:bg-gray-700 text-gray-800 dark:text-white shadow-sm">
                Riwayat Tagihan
            </button>
            <button onclick="switchTab('meteran')" id="tab-meteran"
                class="tab-btn px-4 py-2 text-sm font-semibold rounded-lg transition-all text-gray-500 dark:text-gray-400 hover:text-gray-700">
                Riwayat Meteran
            </button>
        </div>

        {{-- TAB Tagihan --}}
        <div id="panel-tagihan" class="tab-panel">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 dark:text-white">Riwayat Tagihan</h3>
                    <span class="text-xs text-gray-400">{{ $tagihanAir->total() }} tagihan</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="bg-gray-50 dark:bg-gray-800/60">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Periode</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase">Pemakaian</th>
                            <th class="text-right px-3 py-3 text-xs font-semibold text-gray-500 uppercase">Total</th>
                            <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @forelse($tagihanAir as $t)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="px-5 py-3">
                                    <p class="font-semibold text-gray-800 dark:text-gray-200 text-sm">{{ \App\Services\TagihanService::namaBulan($t->bulan) }} {{ $t->tahun }}</p>
                                    <p class="text-xs text-gray-400">{{ $t->nomor_tagihan }}</p>
                                </td>
                                <td class="px-3 py-3 text-center font-semibold text-brand-600 dark:text-brand-400">{{ number_format($t->pemakaian,1) }} m³</td>
                                <td class="px-3 py-3 text-right font-bold text-gray-800 dark:text-white">{{ \App\Services\TagihanService::formatRupiah($t->total_tagihan) }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $t->statusBadge() }}">{{ $t->statusLabel() }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-5 py-10 text-center text-gray-400">Belum ada tagihan</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($tagihanAir->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between flex-wrap gap-3">
                    <p class="text-xs text-gray-400">{{ $tagihanAir->firstItem() }}–{{ $tagihanAir->lastItem() }} dari {{ $tagihanAir->total() }}</p>
                    <div class="flex gap-1 items-center">
                        @if(!$tagihanAir->onFirstPage())<a href="{{ $tagihanAir->previousPageUrl() }}&tab=tagihan" class="px-3 py-1.5 text-xs rounded-lg text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">‹ Prev</a>@endif
                        @foreach($tagihanAir->getUrlRange(max(1,$tagihanAir->currentPage()-2),min($tagihanAir->lastPage(),$tagihanAir->currentPage()+2)) as $page => $url)
                            @if($page==$tagihanAir->currentPage())<span class="px-3 py-1.5 text-xs rounded-lg bg-brand-600 text-white font-semibold">{{ $page }}</span>
                            @else<a href="{{ $url }}&tab=tagihan" class="px-3 py-1.5 text-xs rounded-lg text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">{{ $page }}</a>@endif
                        @endforeach
                        @if($tagihanAir->hasMorePages())<a href="{{ $tagihanAir->nextPageUrl() }}&tab=tagihan" class="px-3 py-1.5 text-xs rounded-lg text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">Next ›</a>@endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- TAB Meteran --}}
        <div id="panel-meteran" class="tab-panel hidden">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 dark:text-white">Riwayat Meteran</h3>
                    <span class="text-xs text-gray-400">{{ $meteranAir->total() }} entri</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="bg-gray-50 dark:bg-gray-800/60">
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Periode</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase">Angka Awal</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase">Angka Akhir</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-gray-500 uppercase">Pemakaian</th>
                            <th class="text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase">Petugas Input</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Tgl Input</th>
                        </tr></thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @forelse($meteranAir as $m)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="px-5 py-3">
                                    <p class="font-semibold text-gray-800 dark:text-gray-200 text-sm">{{ \App\Services\TagihanService::namaBulan($m->bulan) }} {{ $m->tahun }}</p>
                                </td>
                                <td class="px-3 py-3 text-center text-gray-600 dark:text-gray-400">{{ number_format($m->angka_awal) }} m³</td>
                                <td class="px-3 py-3 text-center font-semibold text-gray-800 dark:text-gray-200">{{ number_format($m->angka_akhir) }} m³</td>
                                <td class="px-3 py-3 text-center">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-brand-100 text-brand-700 dark:bg-brand-900/40 dark:text-brand-300">
                                        {{ number_format($m->pemakaian,1) }} m³
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    @if($m->petugas)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-teal-100 dark:bg-teal-900/40 flex items-center justify-center text-teal-700 dark:text-teal-300 text-xs font-bold flex-shrink-0">
                                            {{ strtoupper(substr($m->petugas->nama_petugas,0,1)) }}
                                        </div>
                                        <span class="text-xs text-gray-700 dark:text-gray-300">{{ $m->petugas->nama_petugas }}</span>
                                    </div>
                                    @else
                                    <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <p class="text-xs text-gray-700 dark:text-gray-300">{{ $m->tanggal_baca ? \Carbon\Carbon::parse($m->tanggal_baca)->format('d/m/Y') : '-' }}</p>
                                    <p class="text-xs text-gray-400">{{ $m->created_at ? $m->created_at->format('H:i') : '' }}</p>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">Belum ada data meteran</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($meteranAir->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between flex-wrap gap-3">
                    <p class="text-xs text-gray-400">{{ $meteranAir->firstItem() }}–{{ $meteranAir->lastItem() }} dari {{ $meteranAir->total() }}</p>
                    <div class="flex gap-1 items-center">
                        @if(!$meteranAir->onFirstPage())<a href="{{ $meteranAir->previousPageUrl() }}&tab=meteran" class="px-3 py-1.5 text-xs rounded-lg text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">‹ Prev</a>@endif
                        @foreach($meteranAir->getUrlRange(max(1,$meteranAir->currentPage()-2),min($meteranAir->lastPage(),$meteranAir->currentPage()+2)) as $page => $url)
                            @if($page==$meteranAir->currentPage())<span class="px-3 py-1.5 text-xs rounded-lg bg-brand-600 text-white font-semibold">{{ $page }}</span>
                            @else<a href="{{ $url }}&tab=meteran" class="px-3 py-1.5 text-xs rounded-lg text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">{{ $page }}</a>@endif
                        @endforeach
                        @if($meteranAir->hasMorePages())<a href="{{ $meteranAir->nextPageUrl() }}&tab=meteran" class="px-3 py-1.5 text-xs rounded-lg text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">Next ›</a>@endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal Assign Petugas --}}
<div id="assignModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAssignModal()"></div>
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 w-full max-w-sm p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-gray-800 dark:text-gray-200">Assign Petugas</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ $pelanggan->nama_pelanggan }} · {{ $pelanggan->nomor_pelanggan }}</p>
            </div>
            <button onclick="closeAssignModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @if($pelanggan->petugas)
        <div class="mb-4 px-3 py-2.5 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl text-xs text-blue-700 dark:text-blue-300">
            Petugas saat ini: <strong>{{ $pelanggan->petugas->nama_petugas }}</strong> — dapat diubah.
        </div>
        @endif
        <form method="POST" action="{{ route('admin.pelanggan.assign-petugas', $pelanggan) }}">
            @csrf
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Pilih Petugas</label>
            <select name="petugas_id"
                class="w-full py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all mb-5">
                <option value="">— Tanpa petugas —</option>
                @foreach($petugasList as $pt)
                <option value="{{ $pt->id }}" {{ $pelanggan->petugas_id == $pt->id ? 'selected' : '' }}>{{ $pt->nama_petugas }}</option>
                @endforeach
            </select>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeAssignModal()"
                    class="px-4 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 hover:bg-gray-50 transition-all">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold transition-all">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(name) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('bg-white','dark:bg-gray-700','text-gray-800','dark:text-white','shadow-sm');
        b.classList.add('text-gray-500','dark:text-gray-400');
    });
    document.getElementById('panel-' + name).classList.remove('hidden');
    const btn = document.getElementById('tab-' + name);
    btn.classList.add('bg-white','dark:bg-gray-700','text-gray-800','dark:text-white','shadow-sm');
    btn.classList.remove('text-gray-500','dark:text-gray-400');
}
const activeTab = new URLSearchParams(window.location.search).get('tab') || 'tagihan';
switchTab(activeTab);

function openAssignModal() {
    document.getElementById('assignModal').classList.remove('hidden');
    document.getElementById('assignModal').classList.add('flex');
}
function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
    document.getElementById('assignModal').classList.remove('flex');
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAssignModal(); });
</script>
@endpush