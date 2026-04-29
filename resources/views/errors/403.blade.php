{{-- resources/views/errors/403.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>*{font-family:'DM Sans',sans-serif;}h1,h2{font-family:'Plus Jakarta Sans',sans-serif;}</style>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="text-center max-w-md">
        <div class="w-24 h-24 rounded-3xl bg-red-100 flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h1 class="text-6xl font-extrabold text-red-500 mb-2">403</h1>
        <h2 class="text-2xl font-bold text-gray-800 mb-3">Akses Ditolak</h2>
        <p class="text-gray-500 mb-8">Anda tidak memiliki izin untuk mengakses halaman ini. Pastikan Anda login dengan akun yang tepat.</p>
        <div class="flex gap-3 justify-center">
            <a href="{{ url()->previous() }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all">← Kembali</a>
            @auth
            <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'petugas' ? route('petugas.dashboard') : route('pelanggan.dashboard')) }}"
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all">Dashboard</a>
            @else
            <a href="{{ route('login') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all">Login</a>
            @endauth
        </div>
    </div>
</body>
</html>
