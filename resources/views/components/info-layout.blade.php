@props(['title', 'kicker', 'heading', 'sub' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — {{ config('app.name', 'LPJ Maker') }}</title>

    @include('partials.head-icons')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-slate-800 bg-slate-100 overflow-x-hidden selection:bg-[#6B81A7] selection:text-white">

    <section class="relative bg-[#0b2246] overflow-hidden">
        <img src="{{ asset('images/kelas.jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover -scale-x-100">
        <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(8,37,79,.85)_0%,rgba(8,37,79,.75)_50%,rgba(8,37,79,.65)_100%)]"></div>

        <div class="relative">
            @include('partials.guest-nav')

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 pt-6 pb-12 lg:pt-12 lg:pb-[72px] text-white">
                <p class="text-[#BFDBFE] text-xs font-bold tracking-[0.3em] uppercase">{{ $kicker }}</p>
                <h1 class="mt-3 text-3xl sm:text-4xl lg:text-5xl font-extrabold leading-tight max-w-2xl">{!! $heading !!}</h1>
                @if ($sub)
                    <p class="mt-4 max-w-xl text-base lg:text-[17px] leading-relaxed text-white/90">{{ $sub }}</p>
                @endif
            </div>
        </div>
    </section>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 py-10 lg:py-12">
        {{ $slot }}
    </main>

    <footer class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 py-6 border-t border-slate-200 text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-center gap-3">
        <p>&copy; {{ date('Y') }} LPJ Maker System. All rights reserved.</p>
        <div class="flex gap-6">
            <a href="#" class="hover:text-slate-800 transition">Privacy</a>
            <a href="#" class="hover:text-slate-800 transition">Terms</a>
            <a href="{{ route('bantuan') }}" class="hover:text-slate-800 transition">Help</a>
        </div>
    </footer>
</body>
</html>
