{{-- resources/views/admin/log/index.blade.php --}}
@extends('layouts.app')
@section('title', 'Log Aktivitas')
@section('page_title', 'Log Aktivitas')
@section('page_subtitle', 'Pantau semua aktivitas pengguna sistem')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Aktivitas Hari Ini</p>
        <p class="text-2xl font-extrabold text-blue-600 dark:text-blue-400">{{ $stats['total_hari_ini'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">User Aktif Hari Ini</p>
        <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $stats['user_aktif'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Login Hari Ini</p>
        <p class="text-2xl font-extrabold text-amber-600 dark:text-amber-400">{{ $stats['login_hari_ini'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Aktivitas Minggu Ini</p>
        <p class="text-2xl font-extrabold text-purple-600 dark:text-purple-400">{{ $stats['total_minggu'] }}</p>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-4 mb-4">
    <form method="GET" action="{{ route('admin.log.index') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Tanggal</label>
            <input type="date" name="tanggal" value="{{ request('tanggal', today()->format('Y-m-d')) }}"
                class="px-3 py-2 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">User</label>
            <select name="user_id" class="px-3 py-2 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
                <option value="">Semua User</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                    {{ $u->name }} ({{ $u->role }})
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Role</label>
            <select name="role" class="px-3 py-2 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
                <option value="">Semua Role</option>
                <option value="admin"     {{ request('role') == 'admin'     ? 'selected' : '' }}>Admin</option>
                <option value="petugas"   {{ request('role') == 'petugas'   ? 'selected' : '' }}>Petugas</option>
                <option value="pelanggan" {{ request('role') == 'pelanggan' ? 'selected' : '' }}>Pelanggan</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Kata Kunci Aksi</label>
            <input type="text" name="aksi" value="{{ request('aksi') }}" placeholder="login, input, edit..."
                class="px-3 py-2 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 w-40">
        </div>
        <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow transition-all">
            🔍 Filter
        </button>
        <a href="{{ route('admin.log.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 text-gray-600 dark:text-gray-400 text-sm font-semibold rounded-xl transition-all">
            Reset
        </a>
    </form>
</div>

{{-- Log List --}}
<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <h3 class="font-bold text-gray-800 dark:text-white text-sm">
            Riwayat Aktivitas
            <span class="ml-2 text-xs font-normal text-gray-400">({{ $logs->total() }} total)</span>
        </h3>
    </div>

    <div class="divide-y divide-gray-50 dark:divide-gray-800">
        @forelse($logs as $log)
        @php
            // Tentukan warna & emoji berdasarkan aksi
            $aksi = strtolower($log->aksi);
            if (str_contains($aksi, 'login')) {
                $emoji = '🔑'; $bg = 'bg-blue-50 dark:bg-blue-900/20'; $badge = 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'; $label = 'LOGIN';
            } elseif (str_contains($aksi, 'logout')) {
                $emoji = '🚪'; $bg = 'bg-gray-50 dark:bg-gray-800/40'; $badge = 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'; $label = 'LOGOUT';
            } elseif (str_contains($aksi, 'create') || str_contains($aksi, 'tambah') || str_contains($aksi, 'input')) {
                $emoji = '➕'; $bg = 'bg-emerald-50 dark:bg-emerald-900/10'; $badge = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'; $label = 'TAMBAH';
            } elseif (str_contains($aksi, 'update') || str_contains($aksi, 'edit') || str_contains($aksi, 'ubah')) {
                $emoji = '✏️'; $bg = 'bg-amber-50 dark:bg-amber-900/10'; $badge = 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'; $label = 'EDIT';
            } elseif (str_contains($aksi, 'delete') || str_contains($aksi, 'hapus')) {
                $emoji = '🗑️'; $bg = 'bg-red-50 dark:bg-red-900/10'; $badge = 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'; $label = 'HAPUS';
            } elseif (str_contains($aksi, 'bayar') || str_contains($aksi, 'pembayaran') || str_contains($aksi, 'konfirmasi')) {
                $emoji = '💰'; $bg = 'bg-green-50 dark:bg-green-900/10'; $badge = 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'; $label = 'BAYAR';
            } elseif (str_contains($aksi, 'pengaduan')) {
                $emoji = '📢'; $bg = 'bg-orange-50 dark:bg-orange-900/10'; $badge = 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300'; $label = 'PENGADUAN';
            } else {
                $emoji = '📋'; $bg = 'bg-gray-50 dark:bg-gray-800/20'; $badge = 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400'; $label = strtoupper($log->aksi);
            }

            // Role badge
            $role = $log->user?->role ?? 'system';
            $roleBadge = match($role) {
                'admin'     => 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
                'petugas'   => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                'pelanggan' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                default     => 'bg-gray-100 text-gray-600',
            };
        @endphp
        <div class="flex items-start gap-4 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors {{ $bg }} cursor-pointer"
            onclick="lihatDetail({{ $log->id }}, '{{ addslashes($log->user?->name ?? 'System') }}', '{{ $role }}', '{{ $label }}', '{{ addslashes($log->deskripsi) }}', '{{ $log->created_at->format('d M Y • H:i:s') }}', '{{ $log->ip_address ?? '-' }}', '{{ $log->model ?? '-' }}', '{{ $log->model_id ?? '-' }}')">
            {{-- Icon --}}
            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-lg flex-shrink-0 bg-white dark:bg-gray-900 shadow-sm border border-gray-100 dark:border-gray-800">
                {{ $emoji }}
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                        {{ $log->user?->name ?? 'System' }}
                    </span>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold capitalize {{ $roleBadge }}">
                        {{ $role }}
                    </span>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $badge }}">
                        {{ $label }}
                    </span>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $log->deskripsi }}</p>
                <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                    <span class="text-xs font-mono text-gray-400">
                        🕐 {{ $log->created_at->format('d M Y • H:i:s') }}
                        <span class="text-gray-300">({{ $log->created_at->diffForHumans() }})</span>
                    </span>
                    @if($log->ip_address)
                    <span class="text-xs font-mono text-gray-400">🌐 {{ $log->ip_address }}</span>
                    @endif
                    @if($log->model)
                    <span class="text-xs text-gray-400">📁 {{ $log->model }}{{ $log->model_id ? ' #'.$log->model_id : '' }}</span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="px-5 py-16 text-center text-gray-400">
            <p class="text-4xl mb-3">📋</p>
            <p class="font-semibold">Tidak ada aktivitas</p>
            <p class="text-xs mt-1">Belum ada aktivitas yang tercatat untuk filter ini</p>
        </div>
        @endforelse
    </div>

    @if($logs->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
        {{ $logs->links() }}
    </div>
    @endif
</div>

{{-- Modal Detail --}}
<div id="modal-detail" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 bg-brand-600">
            <h3 class="font-semibold text-white text-sm">📋 Detail Aktivitas</h3>
            <button onclick="document.getElementById('modal-detail').classList.add('hidden')" class="text-white/80 hover:text-white">✕</button>
        </div>
        <div class="px-6 py-5 space-y-3">
            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                <span class="text-xs font-semibold text-gray-400">User</span>
                <span class="text-sm font-semibold text-gray-800 dark:text-white" id="d-user"></span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                <span class="text-xs font-semibold text-gray-400">Role</span>
                <span class="text-sm text-gray-600 dark:text-gray-300 capitalize" id="d-role"></span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                <span class="text-xs font-semibold text-gray-400">Aksi</span>
                <span class="text-sm font-semibold text-gray-800 dark:text-white" id="d-aksi"></span>
            </div>
            <div class="py-2 border-b border-gray-100 dark:border-gray-800">
                <span class="text-xs font-semibold text-gray-400 block mb-1">Deskripsi</span>
                <span class="text-sm text-gray-600 dark:text-gray-300" id="d-deskripsi"></span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                <span class="text-xs font-semibold text-gray-400">Waktu</span>
                <span class="text-xs font-mono text-gray-600 dark:text-gray-300" id="d-waktu"></span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                <span class="text-xs font-semibold text-gray-400">IP Address</span>
                <span class="text-xs font-mono text-gray-600 dark:text-gray-300" id="d-ip"></span>
            </div>
            <div class="flex justify-between items-center py-2">
                <span class="text-xs font-semibold text-gray-400">Model</span>
                <span class="text-xs font-mono text-gray-600 dark:text-gray-300" id="d-model"></span>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex justify-end">
            <button onclick="document.getElementById('modal-detail').classList.add('hidden')"
                class="px-4 py-2 rounded-xl text-sm font-semibold bg-brand-600 hover:bg-brand-700 text-white transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function lihatDetail(id, user, role, aksi, deskripsi, waktu, ip, model, modelId) {
    document.getElementById('d-user').textContent     = user;
    document.getElementById('d-role').textContent     = role;
    document.getElementById('d-aksi').textContent     = aksi;
    document.getElementById('d-deskripsi').textContent = deskripsi;
    document.getElementById('d-waktu').textContent    = waktu;
    document.getElementById('d-ip').textContent       = ip;
    document.getElementById('d-model').textContent    = model !== '-' ? model + (modelId !== '-' ? ' #' + modelId : '') : '-';
    document.getElementById('modal-detail').classList.remove('hidden');
}
</script>
@endpush

@endsection