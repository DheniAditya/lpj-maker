<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>LPJ Maker</title>

    @include('partials.head-icons')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 overflow-x-hidden bg-[#0b2246] selection:bg-[#6B81A7] selection:text-white">

    <div class="relative w-full min-h-screen flex flex-col">

        {{-- Latar foto + overlay navy: paling pekat di belakang teks agar selalu terbaca --}}
        <div class="fixed inset-0 z-0">
            <img src="{{ asset('images/kelas.jpg') }}" alt="" class="w-full h-full object-cover -scale-x-100">
            <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(8,37,79,.82)_0%,rgba(8,37,79,.7)_50%,rgba(8,37,79,.6)_100%)]"></div>
        </div>

        @include('partials.guest-nav')

        <main class="relative z-10 flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 pb-10 pt-4 lg:pt-8 grid grid-cols-1 lg:grid-cols-2 items-center gap-8 lg:gap-12">

            <div class="text-white order-1 lg:order-2">
                <p class="text-[#BFDBFE] tracking-[0.3em] text-[11px] sm:text-xs font-bold uppercase mb-3">
                    Automatic Financial Reporting System
                </p>

                <h1 class="text-4xl sm:text-5xl lg:text-[68px] font-extrabold leading-[1.05] tracking-tight">
                    SMP SMK <br>
                    <span class="text-[#BFDBFE]">IDN Solo</span>
                </h1>

                <h2 class="mt-3 text-base sm:text-xl lg:text-[22px] font-bold text-white/90 uppercase tracking-[0.12em]">
                    Jagoan IT Pinter Ngaji
                </h2>

                <p class="mt-5 text-base lg:text-lg text-white/85 max-w-md leading-relaxed hidden sm:block">
                    Generate PDF financial reports instantly. Deliver on your promises—fast, responsive, and responsible.
                </p>

                @guest
                    <div class="mt-8 hidden sm:flex">
                        <a href="{{ route('register') }}" class="inline-flex items-center min-h-[44px] px-7 py-3 rounded-xl bg-white/15 hover:bg-white/25 border border-white/25 text-white font-bold transition">
                            Join now&rarr;
                        </a>
                    </div>
                @endguest
            </div>

            <div class="order-2 lg:order-1 w-full">
                {{ $slot }}
            </div>

        </main>

        <footer class="relative z-10 w-full px-6 py-6 lg:px-12 text-xs text-white/70">
            <div class="max-w-7xl mx-auto flex flex-col lg:flex-row justify-between items-center gap-4">
                <p>&copy; {{ date('Y') }} LPJ Maker System. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white transition">Privacy</a>
                    <a href="#" class="hover:text-white transition">Terms</a>
                    <a href="{{ route('bantuan') }}" class="hover:text-white transition">Help</a>
                </div>
            </div>
        </footer>

    </div>
    {{-- Halaman yang dipulihkan dari cache (tombol Kembali / buka ulang dari layar utama HP)
         membawa token CSRF lama → muat ulang agar tidak muncul "Page Expired". --}}
    <script>
        window.addEventListener('pageshow', (event) => { if (event.persisted) location.reload(); });
    </script>
</body>
</html>
