{{-- resources/views/petugas/peta.blade.php --}}
@extends('layouts.app')
@section('title', 'Peta Pelanggan')
@section('page_title', 'Peta Pelanggan')
@section('page_subtitle', 'Lihat lokasi semua pelanggan di peta')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
#peta { height: 450px; width: 100%; border-radius: 16px; z-index: 1; }
.pulse { animation: pulse 2s infinite; }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }
</style>
@endpush

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Total Pelanggan</p>
        <p class="text-2xl font-extrabold text-blue-600 dark:text-blue-400">{{ $totalPelanggan }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Ada Koordinat</p>
        <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $adaKoordinat }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 shadow-sm">
        <p class="text-xs text-gray-400 mb-1">Belum Ada Lokasi</p>
        <p class="text-2xl font-extrabold text-red-500 dark:text-red-400">{{ $belumKoordinat }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Peta --}}
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300">Peta Semua Pelanggan</h3>
                <div class="flex gap-3 text-xs text-gray-500">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span> Aktif</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span> Tunggakan</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-gray-400 inline-block"></span> Nonaktif</span>
                </div>
            </div>
            <div id="peta"></div>
        </div>
    </div>

    {{-- Daftar Pelanggan --}}
    <div class="space-y-4">

        {{-- Ada Koordinat --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300">📍 Daftar Lokasi</h3>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800 max-h-80 overflow-y-auto">
                @forelse($pelangganDenganKoordinat as $p)
                <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors cursor-pointer"
                    onclick="fokusKePelanggan({{ $p->latitude }}, {{ $p->longitude }}, '{{ $p->nama_pelanggan }}')">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-8 h-8 rounded-xl flex-shrink-0 flex items-center justify-center text-white text-xs font-bold
                                {{ $p->status === 'aktif' ? 'bg-gradient-to-br from-emerald-400 to-emerald-600' : 'bg-gradient-to-br from-gray-400 to-gray-600' }}">
                                {{ strtoupper(substr($p->nama_pelanggan, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $p->nama_pelanggan }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $p->desa ?? $p->alamat }}</p>
                            </div>
                        </div>
                        <a href="https://www.google.com/maps?q={{ $p->latitude }},{{ $p->longitude }}"
                            target="_blank"
                            onclick="event.stopPropagation()"
                            class="flex-shrink-0 px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition-all">
                            🗺️
                        </a>
                    </div>
                </div>
                @empty
                <div class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada pelanggan dengan koordinat</div>
                @endforelse
            </div>
        </div>

        {{-- Belum Ada Koordinat --}}
        @if($pelangganTanpaKoordinat->count() > 0)
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-bold text-red-500">⚠️ Belum Ada Lokasi</h3>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800 max-h-48 overflow-y-auto">
                @foreach($pelangganTanpaKoordinat as $p)
                <div class="px-4 py-3 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-8 h-8 rounded-xl flex-shrink-0 flex items-center justify-center text-white text-xs font-bold bg-gradient-to-br from-red-400 to-red-600">
                            {{ strtoupper(substr($p->nama_pelanggan, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $p->nama_pelanggan }}</p>
                            <p class="text-xs text-gray-400">Belum ada koordinat</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const peta = L.map('peta').setView([-0.1277, 100.1746], 13);

// Google Maps Hybrid
L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
    attribution: '© Google Maps', maxZoom: 21
}).addTo(peta);

// Data pelanggan dari Laravel
const pelanggan = @json($pelangganDenganKoordinat);

// Warna marker berdasarkan status
function warnaMarker(status, tunggakan) {
    if (tunggakan > 0) return '#EF4444';
    if (status === 'aktif') return '#10B981';
    return '#9CA3AF';
}

// Buat marker untuk setiap pelanggan
pelanggan.forEach(p => {
    const warna = warnaMarker(p.status, p.total_tunggakan ?? 0);
    const icon = L.divIcon({
        className: '',
        html: `<div style="
            width:14px;height:14px;
            background:${warna};
            border:2px solid white;
            border-radius:50%;
            box-shadow:0 1px 4px rgba(0,0,0,0.4);
        "></div>`,
        iconSize: [14, 14],
        iconAnchor: [7, 7],
    });

    const marker = L.marker([p.latitude, p.longitude], { icon })
        .addTo(peta)
        .bindPopup(`
            <div style="font-family:sans-serif;min-width:160px;">
                <p style="font-weight:600;font-size:13px;margin:0 0 4px;">${p.nama_pelanggan}</p>
                <p style="font-size:11px;color:#666;margin:0 0 2px;">${p.nomor_pelanggan}</p>
                <p style="font-size:11px;color:#666;margin:0 0 6px;">${p.desa ?? p.alamat ?? '-'}</p>
                <a href="https://www.google.com/maps?q=${p.latitude},${p.longitude}"
                    target="_blank"
                    style="display:inline-block;padding:4px 10px;background:#059669;color:white;border-radius:6px;font-size:11px;text-decoration:none;font-weight:500;">
                    🗺️ Buka Google Maps
                </a>
            </div>
        `);
});

// Fungsi fokus ke pelanggan dari daftar
function fokusKePelanggan(lat, lng, nama) {
    peta.setView([lat, lng], 18);
    // Buka popup marker yang sesuai
    peta.eachLayer(layer => {
        if (layer instanceof L.Marker) {
            const pos = layer.getLatLng();
            if (Math.abs(pos.lat - lat) < 0.0001 && Math.abs(pos.lng - lng) < 0.0001) {
                layer.openPopup();
            }
        }
    });
}
</script>
@endpush