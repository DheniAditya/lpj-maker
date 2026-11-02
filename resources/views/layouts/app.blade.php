<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <title>{{ config('app.name', 'LPJ Maker') }}</title>
        @include('partials.head-icons')

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    
    <body class="font-sans antialiased text-slate-800 bg-[#0b2246] selection:bg-[#6B81A7] selection:text-white">
        
        <div class="fixed inset-0 -z-10">
            <img src="{{ asset('images/kelas.jpg') }}"
                    alt="Background Building" 
                 class="w-full h-full object-cover">
            
            <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(8,37,79,.5)_0%,rgba(8,37,79,.3)_100%)]"></div>
        </div>
        <div class="min-h-screen">
            
            <div class="sticky top-0 z-50 bg-white/10 backdrop-blur-xl backdrop-saturate-150 border-b border-white/20 shadow-[0_4px_24px_rgba(0,0,0,.08)]">
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
        
        <footer class="py-6 text-center text-xs text-white font-medium relative z-10 drop-shadow-[0_1px_2px_rgba(0,0,0,.5)]">
            &copy; {{ date('Y') }} {{ config('app.name') }}. <span class="opacity-70">Professional Reporting System.</span>
        </footer>
    
        {{-- jQuery & SweetAlert dipakai oleh script halaman (AJAX tanpa reload) --}}
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @stack('scripts')
    </body>
</html>