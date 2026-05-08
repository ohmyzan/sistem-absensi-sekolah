<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Absensi AI - SMK Jakarta 1</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen flex flex-col selection:bg-blue-200 selection:text-blue-900">

    <nav
        class="bg-white/70 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200 px-6 py-4 flex justify-between items-center">
        <div class="font-bold text-xl text-slate-800 flex items-center gap-2">
            <div class="bg-blue-600 p-1.5 rounded-lg text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                    </path>
                </svg>
            </div>
            Hadir<span class="text-blue-600">AI</span>
        </div>
        <div class="text-sm font-semibold text-slate-500 bg-slate-100 px-3 py-1 rounded-full">
            SMK Jakarta 1
        </div>
    </nav>

    <main class="flex-grow flex flex-col items-center justify-center px-4 text-center relative overflow-hidden">

        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[400px] bg-blue-300/30 rounded-full blur-3xl -z-10">
        </div>
        <div class="absolute bottom-0 right-0 w-[400px] h-[300px] bg-indigo-300/20 rounded-full blur-3xl -z-10"></div>

        <div class="max-w-4xl z-10">
            <span
                class="inline-block py-1 px-3 rounded-full bg-blue-100 text-blue-700 text-xs font-bold tracking-wider mb-6 uppercase border border-blue-200">
                Sistem Terintegrasi v1.0
            </span>

            <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 mb-6 tracking-tight leading-tight">
                Absensi Masa Depan <br class="hidden md:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Berbasis Face
                    Recognition</span>
            </h1>

            <p class="text-lg md:text-xl text-slate-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                Tinggalkan cara manual. Tingkatkan efisiensi dan cegah manipulasi data dengan teknologi deteksi wajah
                otomatis yang akurat dan *real-time*.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">

                <a href="{{ route('absensi.index') }}"
                    class="group relative inline-flex items-center justify-center px-8 py-4 text-base font-bold text-white bg-blue-600 rounded-xl overflow-hidden transition-all hover:bg-blue-700 hover:scale-105 shadow-lg shadow-blue-500/30">
                    <svg class="w-6 h-6 mr-2 transition-transform group-hover:rotate-12" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                        </path>
                    </svg>
                    Buka Mesin Absensi
                </a>

                <a href="/admin"
                    class="group inline-flex items-center justify-center px-8 py-4 text-base font-bold text-slate-700 bg-white border border-slate-200 rounded-xl transition-all hover:bg-slate-50 hover:border-slate-300 hover:scale-105 shadow-sm">
                    <svg class="w-6 h-6 mr-2 text-slate-400 group-hover:text-slate-600 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Masuk Panel Admin
                </a>

            </div>
        </div>
    </main>

    <footer class="py-6 text-center text-sm text-slate-400 border-t border-slate-200">
        &copy; 2026 SMK Jakarta 1. Dikembangkan dengan Laravel, Filament & face-api.js.
    </footer>

</body>

</html>
