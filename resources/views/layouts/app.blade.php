<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <link rel="icon" href="{{ asset('build/assets/icon_only_2.png') }}" type="image/png">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    
    <body class="font-sans antialiased text-slate-800 selection:bg-[#6B81A7] selection:text-white">
        
        <div class="fixed inset-0 -z-10">
            <img src="{{ asset('build/assets/kelas.jpg') }}"
                    alt="Background Building" 
                 class="w-full h-full object-cover">
            
            <div class="absolute inset-0 bg-gradient-to-br from-[#08254F]/90 via-[#6B81A7]/80 to-slate-200/50 backdrop-blur-[2px]"></div>
        </div>
        <div class="min-h-screen">
            
            <div class="sticky top-0 z-50 bg-[#6B81A7]/10  backdrop-blur-md border-b border-white/40 shadow-sm">
                @include('layouts.navigation')
            </div>

            @isset($header)
                <header class="relative overflow-hidden bg-[#6B81A7]/10 backdrop-blur-sm border-b border-white/20 shadow-sm">
                    <div class="absolute top-0 left-0 w-full h-1 bg-[#6B81A7]/10 "></div>
                    
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        <div class="font-bold text-2xl text-[#08254F] tracking-tight drop-shadow-sm">
                            {{ $header }}
                        </div>
                        
                        <div class="text-xs font-bold text-[#6B81A7] bg-white/70 px-3 py-1 rounded-full hidden sm:block shadow-sm border border-white/50">
                            {{ date('d M Y') }}
                        </div>
                    </div>
                </header>
            @endisset

            <main class="py-2 px-4">
                {{ $slot }}
            </main>
            
        </div>
        
        <footer class="py-6 text-center text-xs text-slate-600 font-medium relative z-10">
            &copy; {{ date('Y') }} {{ config('app.name') }}. <span class="opacity-70">Professional Reporting System.</span>
        </footer>
    
        <!-- fitur tanpa reload dengan AJAX -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </body>
</html>