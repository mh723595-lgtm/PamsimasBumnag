@extends('layouts.app')
@section('title', 'Assign Petugas ke Jorong')
@section('page_title', 'Assign Petugas ke Jorong')
@section('page_subtitle', 'Kelola pembagian tugas pencatatan meteran per jorong')

@section('content')

<div id="alert-box" class="hidden mb-4">
    <div id="alert-content" class="flex items-center gap-3 p-4 rounded-xl border text-sm font-medium">
        <span id="alert-message"></span>
    </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Total Petugas Aktif</p>
        <p class="text-2xl font-bold text-emerald-500">{{ $stats['total_petugas'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Total Jorong</p>
        <p class="text-2xl font-bold text-blue-500">{{ $stats['total_jorong'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Sudah Diassign</p>
        <p class="text-2xl font-bold text-purple-500">{{ $stats['sudah_assign'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Belum Diassign</p>
        <p class="text-2xl font-bold text-amber-500">{{ $stats['belum_assign'] }}</p>
    </div>
</div>

<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex-wrap gap-3">
        <h2 class="font-semibold text-gray-800 dark:text-white">Daftar Assign Petugas</h2>
        <div class="flex items-center gap-3 flex-wrap">
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('admin.assign-petugas.index') }}"
                    class="px-3 py-1.5 rounded-full text-xs font-medium border transition-colors {{ !request('jorong') ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 border-gray-200 dark:border-gray-700' }}">
                    Semua
                </a>
                @foreach($jorongList as $j)
                <a href="{{ route('admin.assign-petugas.index', ['jorong' => $j->id, 'search' => request('search')]) }}"
                    class="px-3 py-1.5 rounded-full text-xs font-medium border transition-colors {{ request('jorong') == $j->id ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 border-gray-200 dark:border-gray-700' }}">
                    {{ $j->nama_jorong }}
                </a>
                @endforeach
            </div>
            <form method="GET" action="{{ route('admin.assign-petugas.index') }}">
                @if(request('jorong'))<input type="hidden" name="jorong" value="{{ request('jorong') }}">@endif
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari petugas..."
                    class="border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-xl px-3 py-1.5 text-sm w-44 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </form>
            <button onclick="document.getElementById('modal-assign').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Assign Baru
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">Petugas</th>
                    <th class="px-4 py-3 text-left">Jorong</th>
                    <th class="px-4 py-3 text-left">Periode</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Catatan</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($assigns as $i => $a)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center text-emerald-700 font-bold text-xs">
                                {{ strtoupper(substr($a->petugas->nama_petugas, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">{{ $a->petugas->nama_petugas }}</p>
                                <p class="text-xs text-gray-400">{{ $a->petugas->jabatan ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-lg text-xs font-medium">
                            {{ $a->jorong->nama_jorong ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $a->periode }}</td>
                    <td class="px-4 py-3">
                        <button onclick="toggleAktif({{ $a->id }}, this)"
                            data-aktif="{{ $a->aktif ? '1' : '0' }}"
                            class="px-2 py-1 rounded-full text-xs font-semibold transition-colors
                                {{ $a->aktif ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-800' }}">
                            {{ $a->aktif ? 'Aktif' : 'Nonaktif' }}
                        </button>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $a->catatan ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                       <div class="flex items-center justify-center gap-2">
    <button onclick="editAssign({{ $a->id }}, '{{ $a->petugas->nama_petugas }}', {{ $a->jorong_id }}, '{{ $a->periode }}', '{{ $a->catatan }}')"
        class="px-3 py-1.5 text-xs font-medium border border-gray-200 dark:border-gray-700 text-gray-500 hover:border-brand-400 hover:text-brand-500 rounded-lg transition-colors">
        Edit
         </button>
            <button onclick="hapusAssign({{ $a->id }})"
                class="px-3 py-1.5 text-xs font-medium border border-gray-200 dark:border-gray-700 text-gray-500 hover:border-red-400 hover:text-red-500 rounded-lg transition-colors">
                Hapus
            </button>
        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                        <p class="text-3xl mb-2">📋</p>
                        <p class="text-sm">Belum ada assign petugas</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Assign --}}
<div id="modal-assign" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 bg-emerald-500">
            <h3 class="font-semibold text-white">Assign Petugas ke Jorong</h3>
            <button onclick="document.getElementById('modal-assign').classList.add('hidden')" class="text-white/80 hover:text-white">✕</button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Pilih Petugas</label>
                <select id="modal-petugas-id"
                    class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <option value="">-- Pilih petugas --</option>
                    @foreach($petugas as $p)
                    <option value="{{ $p->id }}">{{ $p->nama_petugas }} {{ $p->jabatan ? '('.$p->jabatan.')' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Pilih Jorong</label>
                <select id="modal-jorong"
                    class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <option value="">-- Pilih jorong --</option>
                    @foreach($jorongList as $j)
                    <option value="{{ $j->id }}">{{ $j->nama_jorong }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Periode</label>
                <select id="modal-periode"
                    class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <option value="permanen">Permanen</option>
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ \App\Services\TagihanService::namaBulan($m) . ' ' . now()->year }}">
                        {{ \App\Services\TagihanService::namaBulan($m) }} {{ now()->year }}
                    </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Catatan (opsional)</label>
                <input type="text" id="modal-catatan" placeholder="Catatan khusus..."
                    class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex gap-3 justify-end">
            <button onclick="document.getElementById('modal-assign').classList.add('hidden')"
                class="px-4 py-2 rounded-xl text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">Batal</button>
            <button onclick="simpanAssign()"
                class="px-4 py-2 rounded-xl text-sm font-semibold bg-emerald-500 hover:bg-emerald-600 text-white transition-colors">
                Simpan Assign
            </button>
        </div>
    </div>
</div>

    {{-- Modal Edit --}}
<div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 bg-brand-500">
            <h3 class="font-semibold text-white">Edit Assign Petugas</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-white/80 hover:text-white">✕</button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <input type="hidden" id="edit-id">
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Petugas</label>
                <input type="text" id="edit-nama" readonly
                    class="w-full px-3 py-2.5 text-sm bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-500 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Pilih Jorong</label>
                <select id="edit-jorong-id"
                    class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-400">
                    <option value="">-- Pilih jorong --</option>
                    @foreach($jorongList as $j)
                    <option value="{{ $j->id }}">{{ $j->nama_jorong }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Periode</label>
                <select id="edit-periode"
                    class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-400">
                    <option value="permanen">Permanen</option>
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ \App\Services\TagihanService::namaBulan($m) . ' ' . now()->year }}">
                        {{ \App\Services\TagihanService::namaBulan($m) }} {{ now()->year }}
                    </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Catatan (opsional)</label>
                <input type="text" id="edit-catatan" placeholder="Catatan khusus..."
                    class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-400">
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex gap-3 justify-end">
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                class="px-4 py-2 rounded-xl text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">Batal</button>
            <button onclick="simpanEdit()"
                class="px-4 py-2 rounded-xl text-sm font-semibold bg-brand-500 hover:bg-brand-600 text-white transition-colors">
                Simpan
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showAlert(type, message) {
    const box = document.getElementById('alert-box');
    const content = document.getElementById('alert-content');
    const styles = {
        success: 'bg-emerald-50 border-emerald-200 text-emerald-800',
        danger:  'bg-red-50 border-red-200 text-red-800',
    };
    content.className = `flex items-center gap-3 p-4 rounded-xl border text-sm font-medium ${styles[type]}`;
    document.getElementById('alert-message').textContent = message;
    box.classList.remove('hidden');
    setTimeout(() => box.classList.add('hidden'), 4000);
}

function simpanAssign() {
    const petugasId = document.getElementById('modal-petugas-id').value;
    const jorongId  = document.getElementById('modal-jorong').value;
    const periode   = document.getElementById('modal-periode').value;
    const catatan   = document.getElementById('modal-catatan').value;

    if (!petugasId || !jorongId) {
        showAlert('danger', 'Pilih petugas dan jorong terlebih dahulu!');
        return;
    }

    fetch('{{ route("admin.assign-petugas.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            petugas_id: petugasId,
            jorong_id: jorongId,
            periode: periode,
            catatan: catatan,
        })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('modal-assign').classList.add('hidden');
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(() => showAlert('danger', 'Terjadi kesalahan. Coba lagi.'));
}

function hapusAssign(id) {
    if (!confirm('Yakin hapus assign petugas ini?')) return;
    fetch(`/admin/assign-petugas/${id}`, {
        method: 'DELETE',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(() => showAlert('danger', 'Terjadi kesalahan. Coba lagi.'));
}

function toggleAktif(id, btn) {
    fetch(`/admin/assign-petugas/${id}/toggle`, {
        method: 'PATCH',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const aktif = data.aktif;
            btn.textContent = aktif ? 'Aktif' : 'Nonaktif';
            btn.className = `px-2 py-1 rounded-full text-xs font-semibold transition-colors ${
                aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'
            }`;
        }
    })
    .catch(() => showAlert('danger', 'Terjadi kesalahan.'));

}
    function editAssign(id, nama, jorongId, periode, catatan) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-nama').value = nama;
    document.getElementById('edit-jorong-id').value = jorongId;
    document.getElementById('edit-periode').value = periode;
    document.getElementById('edit-catatan').value = catatan;
    document.getElementById('modal-edit').classList.remove('hidden');
}

function simpanEdit() {
    const id       = document.getElementById('edit-id').value;
    const jorongId = document.getElementById('edit-jorong-id').value;
    const periode  = document.getElementById('edit-periode').value;
    const catatan  = document.getElementById('edit-catatan').value;

    if (!jorongId) {
        showAlert('danger', 'Pilih jorong terlebih dahulu!');
        return;
    }

    fetch(`/admin/assign-petugas/${id}`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ jorong_id: jorongId, periode: periode, catatan: catatan })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('modal-edit').classList.add('hidden');
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(() => showAlert('danger', 'Terjadi kesalahan. Coba lagi.'));
}

</script>
@endpush 