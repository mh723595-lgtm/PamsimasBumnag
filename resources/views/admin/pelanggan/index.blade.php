{{-- resources/views/admin/pelanggan/index.blade.php --}}
@extends('layouts.app')
@section('title','Manajemen Pelanggan')
@section('page_title','Manajemen Pelanggan')
@section('page_subtitle','Kelola data semua pelanggan')

@section('content')

@if(session('success'))
<div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
    class="mb-4 flex items-center gap-3 px-4 py-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 rounded-xl text-green-800 dark:text-green-300 text-sm">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- Stat cards --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    @foreach([['Total',$stats['total'],'blue'],['Aktif',$stats['aktif'],'emerald'],['Nonaktif',$stats['nonaktif'],'red']] as [$l,$v,$c])
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">{{ $l }}</p>
        <p class="text-xl font-extrabold text-{{ $c }}-600 dark:text-{{ $c }}-400">{{ $v }}</p>
    </div>
    @endforeach
</div>

<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">

    {{-- Filter bar --}}
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex flex-wrap gap-3 items-end justify-between">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / no. pelanggan / HP..."
                    class="pl-9 pr-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all w-60">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <select name="status" class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                <option value="">Semua Status</option>
                <option value="aktif"    {{ request('status')==='aktif'    ? 'selected':'' }}>Aktif</option>
                <option value="nonaktif" {{ request('status')==='nonaktif' ? 'selected':'' }}>Nonaktif</option>
                <option value="tutup"    {{ request('status')==='tutup'    ? 'selected':'' }}>Tutup</option>
            </select>
            <select name="jorong_id" class="py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                <option value="">Semua Jorong</option>
                @foreach($jorongList as $j)
                <option value="{{ $j->id }}" {{ request('jorong_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jorong }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow transition-all">Filter</button>
        </form>
        <a href="{{ route('admin.pelanggan.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Pelanggan
        </a>
    </div>

    {{-- Bulk action bar --}}
    <div id="bulkBar" class="hidden px-5 py-3 bg-brand-50 dark:bg-brand-900/20 border-b border-brand-100 dark:border-brand-800 flex items-center gap-3 flex-wrap">
        <span class="text-sm font-medium text-brand-700 dark:text-brand-300"><span id="bulkCount">0</span> pelanggan dipilih</span>
        <div class="flex gap-2 ml-auto">
            <button onclick="openBulkModal()"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg transition-all">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Assign Petugas
            </button>
            <form id="bulkHapusForm" method="POST" action="{{ route('admin.pelanggan.bulk-assign-petugas') }}">
                @csrf
                <input type="hidden" name="petugas_id" value="">
                <div id="bulkHapusIds"></div>
                <button type="button" onclick="submitBulkHapus()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 text-xs font-semibold rounded-lg transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/></svg>
                    Hapus Petugas
                </button>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/60">
                    <th class="px-4 py-3.5 w-10">
                        <input type="checkbox" id="cbAll" onchange="toggleAll(this.checked)"
                            class="w-4 h-4 rounded border-gray-300 text-brand-600 cursor-pointer focus:ring-brand-500">
                    </th>
                    <th class="text-left px-4 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. / Nama</th>
                    <th class="text-left px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Alamat</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">No. HP</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Meteran Awal</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Petugas</th>
                    <th class="text-center px-3 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-center px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($pelanggan as $p)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                    <td class="px-4 py-3.5">
                        <input type="checkbox" value="{{ $p->id }}" onchange="toggleRow(this)"
                            class="cb-row w-4 h-4 rounded border-gray-300 text-brand-600 cursor-pointer focus:ring-brand-500">
                    </td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($p->nama_pelanggan,0,2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ $p->nama_pelanggan }}</p>
                                <p class="font-mono text-xs text-brand-600 dark:text-brand-400">{{ $p->nomor_pelanggan }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-3.5 text-gray-600 dark:text-gray-400 text-xs max-w-[160px] truncate">{{ $p->alamat }}</td>
                    <td class="px-3 py-3.5 text-center text-xs text-gray-500">{{ $p->no_hp ?? '-' }}</td>
                    <td class="px-3 py-3.5 text-center font-semibold text-gray-700 dark:text-gray-300">{{ number_format($p->meteran_awal) }}</td>
                    <td class="px-3 py-3.5 text-center">
                        @if($p->petugas)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $p->petugas->nama_petugas }}
                        </span>
                        @else
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-400 dark:bg-gray-800">Belum</span>
                        @endif
                    </td>
                    <td class="px-3 py-3.5 text-center">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold capitalize
                            {{ $p->status==='aktif' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300'
                            : ($p->status==='nonaktif' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300'
                            : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300') }}">
                            {{ $p->status }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-center gap-1.5">
                            {{-- Detail --}}
                            <a href="{{ route('admin.pelanggan.show', $p) }}" class="p-1.5 rounded-lg text-brand-600 hover:bg-brand-50 dark:hover:bg-brand-900/30 transition-all" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            {{-- Assign Petugas cepat --}}
                            <button onclick="openAssignModal({{ $p->id }}, '{{ addslashes($p->nama_pelanggan) }}', '{{ $p->nomor_pelanggan }}', {{ $p->petugas_id ?? 'null' }})"
                                class="p-1.5 rounded-lg text-teal-600 hover:bg-teal-50 dark:hover:bg-teal-900/30 transition-all" title="Assign Petugas">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </button>
                            {{-- Edit --}}
                            <a href="{{ route('admin.pelanggan.edit', $p) }}" class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/30 transition-all" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            {{-- Hapus --}}
                            <form method="POST" action="{{ route('admin.pelanggan.destroy', $p) }}" onsubmit="return confirm('Hapus pelanggan {{ $p->nama_pelanggan }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-16 text-center text-gray-400">Tidak ada pelanggan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($pelanggan->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between flex-wrap gap-3">
        <p class="text-xs text-gray-400">Menampilkan {{ $pelanggan->firstItem() }}–{{ $pelanggan->lastItem() }} dari {{ $pelanggan->total() }} pelanggan</p>
        <div class="flex gap-1 items-center">
            @if($pelanggan->onFirstPage())
                <span class="px-3 py-1.5 text-xs rounded-lg text-gray-300 dark:text-gray-600 cursor-default">‹ Prev</span>
            @else
                <a href="{{ $pelanggan->previousPageUrl() }}" class="px-3 py-1.5 text-xs rounded-lg text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">‹ Prev</a>
            @endif
            @foreach($pelanggan->getUrlRange(max(1,$pelanggan->currentPage()-2), min($pelanggan->lastPage(),$pelanggan->currentPage()+2)) as $page => $url)
                @if($page == $pelanggan->currentPage())
                    <span class="px-3 py-1.5 text-xs rounded-lg bg-brand-600 text-white font-semibold">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="px-3 py-1.5 text-xs rounded-lg text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">{{ $page }}</a>
                @endif
            @endforeach
            @if($pelanggan->hasMorePages())
                <a href="{{ $pelanggan->nextPageUrl() }}" class="px-3 py-1.5 text-xs rounded-lg text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all">Next ›</a>
            @else
                <span class="px-3 py-1.5 text-xs rounded-lg text-gray-300 dark:text-gray-600 cursor-default">Next ›</span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Modal Assign Petugas --}}
<div id="assignModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAssignModal()"></div>
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 w-full max-w-md p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-gray-800 dark:text-gray-200" id="modalTitle">Assign Petugas</h3>
                <p class="text-xs text-gray-400 mt-0.5" id="modalSubtitle"></p>
            </div>
            <button onclick="closeAssignModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="currentPetugasInfo" class="hidden mb-4 px-3 py-2.5 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl text-xs text-blue-700 dark:text-blue-300">
            Petugas saat ini: <strong id="currentPetugasName"></strong> — dapat diubah atau dihapus.
        </div>
        <form id="assignForm" method="POST">
            @csrf
            <div id="bulkIdsContainer"></div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Pilih Petugas</label>
            <select name="petugas_id" id="petugasSelect"
                class="w-full py-2.5 px-3 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all mb-5">
                <option value="">— Tanpa petugas —</option>
                @foreach($petugasList as $pt)
                <option value="{{ $pt->id }}">{{ $pt->nama_petugas }}</option>
                @endforeach
            </select>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeAssignModal()"
                    class="px-4 py-2 text-sm rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 transition-all">Batal</button>
                <button type="button" id="btnHapusPetugas" onclick="submitHapusPetugas()"
                    class="hidden px-4 py-2 text-sm rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold transition-all">Hapus Petugas</button>
                <button type="submit"
                    class="px-4 py-2 text-sm rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-semibold transition-all">Simpan</button>
            </div>
        </form>
    </div>
</div>

<form id="hapusPetugasForm" method="POST" class="hidden">@csrf @method('DELETE')</form>

@endsection

@push('scripts')
<script>
const ROUTE_ASSIGN      = '{{ route("admin.pelanggan.assign-petugas", ":id") }}';
const ROUTE_HAPUS       = '{{ route("admin.pelanggan.hapus-petugas", ":id") }}';
const ROUTE_BULK_ASSIGN = '{{ route("admin.pelanggan.bulk-assign-petugas") }}';

let isBulkMode = false;
let currentSingleId = null;

function getChecked() {
    return Array.from(document.querySelectorAll('.cb-row:checked')).map(c => c.value);
}
function toggleAll(checked) {
    document.querySelectorAll('.cb-row').forEach(c => c.checked = checked);
    updateBulkBar();
}
function toggleRow(cb) {
    const all = document.querySelectorAll('.cb-row');
    document.getElementById('cbAll').checked = Array.from(all).every(c => c.checked);
    const row = cb.closest('tr');
    cb.checked ? row.classList.add('bg-brand-50','dark:bg-brand-900/10') : row.classList.remove('bg-brand-50','dark:bg-brand-900/10');
    updateBulkBar();
}
function updateBulkBar() {
    const ids = getChecked();
    const bar = document.getElementById('bulkBar');
    document.getElementById('bulkCount').textContent = ids.length;
    bar.classList.toggle('hidden', ids.length === 0);
    bar.classList.toggle('flex',   ids.length > 0);
}
function openAssignModal(pelangganId, nama, nomor, currentPetugasId) {
    isBulkMode = false; currentSingleId = pelangganId;
    document.getElementById('modalTitle').textContent    = 'Assign Petugas';
    document.getElementById('modalSubtitle').textContent = `${nama} · ${nomor}`;
    document.getElementById('assignForm').action         = ROUTE_ASSIGN.replace(':id', pelangganId);
    document.getElementById('bulkIdsContainer').innerHTML = '';
    const infoBox  = document.getElementById('currentPetugasInfo');
    const btnHapus = document.getElementById('btnHapusPetugas');
    if (currentPetugasId) {
        const opt = document.querySelector(`#petugasSelect option[value="${currentPetugasId}"]`);
        document.getElementById('petugasSelect').value = currentPetugasId;
        document.getElementById('currentPetugasName').textContent = opt ? opt.textContent : '—';
        infoBox.classList.remove('hidden'); btnHapus.classList.remove('hidden');
    } else {
        document.getElementById('petugasSelect').value = '';
        infoBox.classList.add('hidden'); btnHapus.classList.add('hidden');
    }
    document.getElementById('assignModal').classList.remove('hidden');
    document.getElementById('assignModal').classList.add('flex');
}
function openBulkModal() {
    const ids = getChecked(); if (!ids.length) return;
    isBulkMode = true; currentSingleId = null;
    document.getElementById('modalTitle').textContent    = 'Assign Petugas (Massal)';
    document.getElementById('modalSubtitle').textContent = `${ids.length} pelanggan dipilih`;
    document.getElementById('assignForm').action         = ROUTE_BULK_ASSIGN;
    document.getElementById('petugasSelect').value       = '';
    document.getElementById('currentPetugasInfo').classList.add('hidden');
    document.getElementById('btnHapusPetugas').classList.add('hidden');
    document.getElementById('bulkIdsContainer').innerHTML = ids.map(id => `<input type="hidden" name="pelanggan_ids[]" value="${id}">`).join('');
    document.getElementById('assignModal').classList.remove('hidden');
    document.getElementById('assignModal').classList.add('flex');
}
function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
    document.getElementById('assignModal').classList.remove('flex');
}
function submitHapusPetugas() {
    if (!currentSingleId) return;
    const form = document.getElementById('hapusPetugasForm');
    form.action = ROUTE_HAPUS.replace(':id', currentSingleId);
    closeAssignModal(); form.submit();
}
function submitBulkHapus() {
    const ids = getChecked(); if (!ids.length) return;
    if (!confirm(`Hapus petugas dari ${ids.length} pelanggan?`)) return;
    const container = document.getElementById('bulkHapusIds');
    container.innerHTML = ids.map(id => `<input type="hidden" name="pelanggan_ids[]" value="${id}">`).join('');
    document.getElementById('bulkHapusForm').submit();
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAssignModal(); });
</script>
@endpush