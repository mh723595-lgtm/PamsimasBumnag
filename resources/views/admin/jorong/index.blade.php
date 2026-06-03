@extends('layouts.app')
@section('title', 'Kelola Jorong')
@section('page_title', 'Kelola Jorong')
@section('page_subtitle', 'Tambah dan kelola data jorong di Nagari Bayua')

@section('content')

<div id="alert-box" class="hidden mb-4">
    <div id="alert-content" class="flex items-center gap-3 p-4 rounded-xl border text-sm font-medium">
        <span id="alert-message"></span>
    </div>
</div>

<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-800">
        <h2 class="font-semibold text-gray-800 dark:text-white">Daftar Jorong</h2>
        <button onclick="document.getElementById('modal-tambah').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Jorong
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">#</th>
                    <th class="px-4 py-3 text-left">Nama Jorong</th>
                    <th class="px-4 py-3 text-left">Kode</th>
                    <th class="px-4 py-3 text-left">Keterangan</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                @forelse($jorong as $i => $j)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-white">{{ $j->nama_jorong }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg text-xs font-mono">
                            {{ $j->kode_jorong ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $j->keterangan ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <button onclick="toggleAktif({{ $j->id }}, this)"
                            class="px-2 py-1 rounded-full text-xs font-semibold transition-colors
                                {{ $j->aktif ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-800' }}">
                            {{ $j->aktif ? 'Aktif' : 'Nonaktif' }}
                        </button>
                    </td>
                    <td class="px-4 py-3 text-center flex items-center justify-center gap-2">
                        <button onclick="editJorong({{ $j->id }}, '{{ $j->nama_jorong }}', '{{ $j->kode_jorong }}', '{{ $j->keterangan }}')"
                            class="px-3 py-1.5 text-xs font-medium border border-blue-200 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                            Edit
                        </button>
                        <button onclick="hapusJorong({{ $j->id }})"
                            class="px-3 py-1.5 text-xs font-medium border border-gray-200 dark:border-gray-700 text-gray-500 hover:border-red-400 hover:text-red-500 rounded-lg transition-colors">
                            Hapus
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                        <p class="text-3xl mb-2">🗺️</p>
                        <p class="text-sm">Belum ada jorong. Tambahkan jorong pertama!</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Tambah --}}
<div id="modal-tambah" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 bg-emerald-500">
            <h3 class="font-semibold text-white">Tambah Jorong Baru</h3>
            <button onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="text-white/80 hover:text-white">✕</button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Nama Jorong <span class="text-red-500">*</span></label>
                <input type="text" id="tambah-nama" placeholder="Contoh: Jorong Koto"
                    class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Kode Jorong</label>
                <input type="text" id="tambah-kode" placeholder="Contoh: JRG-001"
                    class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Keterangan</label>
                <input type="text" id="tambah-keterangan" placeholder="Keterangan opsional..."
                    class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex gap-3 justify-end">
            <button onclick="document.getElementById('modal-tambah').classList.add('hidden')"
                class="px-4 py-2 rounded-xl text-sm text-gray-600 hover:bg-gray-100 transition-colors">Batal</button>
            <button onclick="simpanTambah()"
                class="px-4 py-2 rounded-xl text-sm font-semibold bg-emerald-500 hover:bg-emerald-600 text-white transition-colors">
                Simpan
            </button>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modal-edit" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 bg-blue-500">
            <h3 class="font-semibold text-white">Edit Jorong</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-white/80 hover:text-white">✕</button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <input type="hidden" id="edit-id">
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Nama Jorong <span class="text-red-500">*</span></label>
                <input type="text" id="edit-nama"
                    class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Kode Jorong</label>
                <input type="text" id="edit-kode"
                    class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1.5">Keterangan</label>
                <input type="text" id="edit-keterangan"
                    class="w-full px-3 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex gap-3 justify-end">
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')"
                class="px-4 py-2 rounded-xl text-sm text-gray-600 hover:bg-gray-100 transition-colors">Batal</button>
            <button onclick="simpanEdit()"
                class="px-4 py-2 rounded-xl text-sm font-semibold bg-blue-500 hover:bg-blue-600 text-white transition-colors">
                Simpan Perubahan
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

function simpanTambah() {
    const nama = document.getElementById('tambah-nama').value;
    if (!nama) { showAlert('danger', '❌ Nama jorong wajib diisi!'); return; }

    fetch('{{ route("admin.jorong.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({
            nama_jorong: nama,
            kode_jorong: document.getElementById('tambah-kode').value,
            keterangan:  document.getElementById('tambah-keterangan').value,
        }),
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('modal-tambah').classList.add('hidden');
        if (data.success) {
            showAlert('success', '✅ ' + data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', '❌ ' + (data.message || 'Terjadi kesalahan.'));
        }
    })
    .catch(() => showAlert('danger', '❌ Terjadi kesalahan.'));
}

function editJorong(id, nama, kode, keterangan) {
    document.getElementById('edit-id').value          = id;
    document.getElementById('edit-nama').value        = nama;
    document.getElementById('edit-kode').value        = kode === 'null' ? '' : kode;
    document.getElementById('edit-keterangan').value  = keterangan === 'null' ? '' : keterangan;
    document.getElementById('modal-edit').classList.remove('hidden');
}

function simpanEdit() {
    const id   = document.getElementById('edit-id').value;
    const nama = document.getElementById('edit-nama').value;
    if (!nama) { showAlert('danger', '❌ Nama jorong wajib diisi!'); return; }

    fetch(`/admin/jorong/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({
            nama_jorong: nama,
            kode_jorong: document.getElementById('edit-kode').value,
            keterangan:  document.getElementById('edit-keterangan').value,
        }),
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('modal-edit').classList.add('hidden');
        if (data.success) {
            showAlert('success', '✅ ' + data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', '❌ ' + (data.message || 'Terjadi kesalahan.'));
        }
    })
    .catch(() => showAlert('danger', '❌ Terjadi kesalahan.'));
}

function hapusJorong(id) {
    if (!confirm('Yakin hapus jorong ini?')) return;
    fetch(`/admin/jorong/${id}`, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showAlert('success', '✅ ' + data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', '❌ ' + data.message);
        }
    })
    .catch(() => showAlert('danger', '❌ Terjadi kesalahan.'));
}

function toggleAktif(id, btn) {
    fetch(`/admin/jorong/${id}/toggle`, {
        method: 'PATCH',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            btn.textContent = data.aktif ? 'Aktif' : 'Nonaktif';
            btn.className   = 'px-2 py-1 rounded-full text-xs font-semibold transition-colors ' +
                (data.aktif ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500');
        }
    });
}
</script>
@endpush