{{-- Navigasi halaman publik (beranda, masuk, daftar, fitur, cara pakai, bantuan) --}}
<header x-data="{ open: false }" class="relative z-50 w-full">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 min-h-[72px] lg:min-h-[80px] flex items-center justify-between gap-4">
        <a href="{{ route('lpj.create') }}" class="flex items-center gap-2 shrink-0">
            <img src="{{ asset('build/assets/icon_only_2.png') }}" alt="Logo" class="h-7 md:h-8 w-auto drop-shadow-md">
            <span class="md:text-xl text-base font-light text-white tracking-wide">MAKER</span>
        </a>

        <nav class="hidden md:flex items-center gap-6" aria-label="Menu utama">
            <a href="{{ route('fitur') }}" class="text-sm font-semibold transition {{ request()->routeIs('fitur') ? 'text-white' : 'text-white/85 hover:text-white' }}">Fitur</a>
            <a href="{{ route('cara-pakai') }}" class="text-sm font-semibold transition {{ request()->routeIs('cara-pakai') ? 'text-white' : 'text-white/85 hover:text-white' }}">Cara Pakai</a>
            <a href="{{ route('bantuan') }}" class="text-sm font-semibold transition {{ request()->routeIs('bantuan') ? 'text-white' : 'text-white/85 hover:text-white' }}">Bantuan</a>
            @auth
                <a href="{{ url('/dashboard') }}" class="inline-flex items-center min-h-[40px] px-5 rounded-xl bg-white text-[#0f172a] text-sm font-bold hover:bg-slate-100 transition">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center min-h-[40px] px-5 rounded-xl bg-white text-[#0f172a] text-sm font-bold hover:bg-slate-100 transition">Masuk</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-flex items-center min-h-[40px] px-5 rounded-xl bg-[#0B6BD3] text-white text-sm font-bold hover:bg-[#0950A0] transition">Daftar</a>
                @endif
            @endauth
        </nav>

        <div class="flex md:hidden items-center gap-2">
            @auth
                <a href="{{ url('/dashboard') }}" class="inline-flex items-center min-h-[36px] px-3.5 rounded-lg bg-white text-[#0f172a] text-xs font-bold">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center min-h-[36px] px-3.5 rounded-lg bg-white text-[#0f172a] text-xs font-bold">Masuk</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-flex items-center min-h-[36px] px-3.5 rounded-lg bg-[#0B6BD3] text-white text-xs font-bold">Daftar</a>
                @endif
            @endauth
            <button type="button" @click="open = !open" :aria-expanded="open" aria-label="Menu" class="p-2 rounded-lg text-white hover:bg-white/10">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path x-show="open" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <div x-show="open" x-cloak x-transition.opacity class="md:hidden px-4 pb-3">
        <div class="rounded-2xl bg-[#0f2a4a]/95 border border-white/10 p-2 flex flex-col">
            <a href="{{ route('fitur') }}" class="px-4 py-3 rounded-xl text-sm font-semibold text-white hover:bg-white/10">Fitur</a>
            <a href="{{ route('cara-pakai') }}" class="px-4 py-3 rounded-xl text-sm font-semibold text-white hover:bg-white/10">Cara Pakai</a>
            <a href="{{ route('bantuan') }}" class="px-4 py-3 rounded-xl text-sm font-semibold text-white hover:bg-white/10">Bantuan</a>
        </div>
    </div>
</header>
