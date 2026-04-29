<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>*{font-family:'DM Sans',sans-serif;}h1,h2{font-family:'Plus Jakarta Sans',sans-serif;}</style>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="text-center max-w-md">
        <div class="w-24 h-24 rounded-3xl bg-amber-100 flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="text-6xl font-extrabold text-amber-500 mb-2">404</h1>
        <h2 class="text-2xl font-bold text-gray-800 mb-3">Halaman Tidak Ditemukan</h2>
        <p class="text-gray-500 mb-8">Halaman yang Anda cari tidak ada atau telah dipindahkan. Periksa kembali URL atau kembali ke halaman sebelumnya.</p>
        <div class="flex gap-3 justify-center">
            <a href="{{ url()->previous() }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all">← Kembali</a>
            <a href="{{ route('landing') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all">🏠 Beranda</a>
        </div>
    </div>
</body>
</html>
