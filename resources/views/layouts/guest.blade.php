<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'lpj maker') }}</title>
    
    <link rel="icon" href="{{ asset('build/assets/icon_only_2.png') }}" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 overflow-x-hidden selection:bg-[#6B81A7] selection:text-white">
    
    <div class="relative w-full min-h-screen flex flex-col">

        <div class="fixed inset-0 z-0">
            <img src="{{ asset('build/assets/kelas.jpg') }}"
                alt="Background Building" 
                 class="w-full h-full object-cover -scale-x-100 ">
            
            <div class="absolute inset-0 bg-gradient-to-r from-[#6B81A7]/95 via-[#6B81A7]/80 to-transparent"></div>
        </div>

        <header class="fixed top-0 left-0 w-full z-50 p-6 lg:px-12 flex justify-between items-center 
               backdrop-blur-sm bg-[#6B81A7]/10 
             shadow-sm transition-all duration-300">
                 <a href="/" class="flex items-center gap-2 group">
         <img src="{{ asset('build/assets/icon_only_2.png') }}" alt="Logo" class="h-6 md:h-10 w-auto drop-shadow-md brightness-200 grayscale sm:grayscale-0 sm:brightness-100 transition">
         <span class="md:text-xl text-base font-light text-white tracking-wide">MAKER</span>
    </a>

    <nav class="flex items-center gap-4">
         @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-white/90 hover:text-white transition">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="px-6 py-2.5 md:text-sm text-xs  bg-transparent text-white/90 rounded-full hover:bg-white hover:text-blue-400 transition shadow-lg">
                    Log in
                </a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="px-6 py-2.5 md:text-sm text-xs  bg-transparent text-white/90 rounded-full hover:bg-white hover:text-blue-400 transition shadow-lg">
                        Sign Up
                    </a>
                @endif
            @endauth
        @endif
    </nav>

</header>
        <main class=" relative z-10 flex-grow w-full max-w-7xl mx-auto pt-20 px-6 lg:px-12z grid grid-cols-1 lg:grid-cols-2 items-center gap-4">
            
            <div class="pl-8 flex flex-col justify-center text-white space-y-2 order-2">
                <p class="text-blue-100 tracking-[0.3em] text-xs font-bold uppercase mb-2 ml-1">
                    Automatic Financial Reporting System
                </p>
                
                <h1 class="text-5xl md:text-7xl font-extrabold leading-tight tracking-tight drop-shadow-sm">
                    SMP SMK <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-white to-blue-200">
                        IDN Solo
                    </span>
                </h1>
                
                <h2 class="text-lg md:text-3xl font-bold text-white/60 uppercase tracking-widest mt-[-0.5rem] blur-[1px] select-none">
                    Jagoan IT Pinter Ngaji
                </h2>

                <p class="mt-6 text-lg text-white/90 max-w-md font-light leading-relaxed">
                    Generate PDF financial reports instantly. Deliver on your promises—fast, responsive, and responsible.
                </p>

                <div class="mt-8 flex gap-4">
                    <a href="{{ route('register') }}" class="px-8 py-3 bg-[#566987] text-white font-bold rounded-lg hover:bg-[#465670] transition shadow-xl border border-white/10">
                        Join now&rarr;
                    </a>
                </div>
            </div>

    <div class="px-auto order-1 py-8 bg-[#6B81A7]/30 backdrop-blur-xl border border-white/30 rounded-3xl shadow-2xl">  
            {{ $slot }}
    </div>

        </main>

        <footer class="relative z-10 w-full p-6 text-center lg:text-left lg:px-12 text-xs text-white/60">
            <div class="flex flex-col lg:flex-row justify-between items-center gap-4">
                <p>&copy; {{ date('Y') }} LPJ Maker System. All rights reserved.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white transition">Privacy</a>
                    <a href="#" class="hover:text-white transition">Terms</a>
                    <a href="#" class="hover:text-white transition">Help</a>
                </div>
            </div>
        </footer>

    </div>
</body>
</html>