<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Kesalahan Server</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>*{font-family:'DM Sans',sans-serif;}h1,h2{font-family:'Plus Jakarta Sans',sans-serif;}</style>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="text-center max-w-md">
        <div class="w-24 h-24 rounded-3xl bg-red-100 flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <h1 class="text-6xl font-extrabold text-red-500 mb-2">500</h1>
        <h2 class="text-2xl font-bold text-gray-800 mb-3">Kesalahan Server</h2>
        <p class="text-gray-500 mb-8">Terjadi kesalahan pada server. Tim teknisi kami telah diberitahu. Coba lagi beberapa saat atau hubungi administrator.</p>
        <div class="flex gap-3 justify-center">
            <a href="{{ route('landing') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all">🏠 Beranda</a>
            <a href="javascript:location.reload()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all">↻ Coba Lagi</a>
        </div>
    </div>
</body>
</html>
