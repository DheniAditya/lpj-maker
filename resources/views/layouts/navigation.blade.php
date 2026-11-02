<nav x-data="{ open: false }" 
class="w-full [text-shadow:0_1px_3px_rgba(0,0,0,.6)]">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            
            <div class="flex">
    <div class="shrink-0 flex items-center overflow-visible transition hover:scale-105">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            {{-- Ikon --}}
            <img src="{{ asset('images/icon_only_2.png') }}" alt="Logo" 
                 class="block h-9 w-auto drop-shadow-[0_1px_3px_rgba(0,0,0,.6)] brightness-200 grayscale sm:grayscale-0 sm:brightness-100 transition" />
            
            {{-- Teks (Berada di kanan ikon karena gap-2 dan flex) --}}
            <span class="md:text-xl text-base font-light text-white tracking-wide">MAKER</span>
        </a>
    </div>
</div>

            <div class="hidden sm:flex sm:items-center sm:gap-6 sm:ms-6">
                <a href="{{ route('fitur') }}" class="text-sm font-semibold text-white/85 hover:text-white transition">Fitur</a>
                <a href="{{ route('cara-pakai') }}" class="text-sm font-semibold text-white/85 hover:text-white transition">Cara Pakai</a>
                <a href="{{ route('bantuan') }}" class="text-sm font-semibold text-white/85 hover:text-white transition">Bantuan</a>
                @auth
                    <x-dropdown align="right" width="48" contentClasses="py-1 bg-white [text-shadow:none]">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-bold rounded-full text-white bg-white/10 hover:bg-white/20 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                @if ($pendingResetRequests)
                                    <span class="ms-1.5 w-2 h-2 rounded-full bg-rose-500" title="Ada permintaan reset password"></span>
                                @endif
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('dashboard')">Dashboard</x-dropdown-link>
                            @if (Auth::user()->isAdmin())
                                <x-dropdown-link :href="route('admin.users.index')">
                                    <span class="flex items-center justify-between gap-2">
                                        Panel Admin
                                        @if ($pendingResetRequests)
                                            <span class="min-w-5 h-5 px-1.5 rounded-full bg-rose-500 text-white text-[11px] font-bold flex items-center justify-center" title="Permintaan reset password">{{ $pendingResetRequests }}</span>
                                        @endif
                                    </span>
                                </x-dropdown-link>
                            @endif
                            <x-dropdown-link :href="route('profile.edit')">
                                Edit Akun
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    Keluar
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="flex gap-3">
                        <a href="{{ route('login') }}" class="text-sm font-bold bg-white text-[#0F172A] px-4 py-2 rounded-full hover:bg-gray-100 transition shadow-lg">
                            Masuk
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="text-sm font-bold bg-[#0B6BD3] text-white px-4 py-2 rounded-full hover:bg-[#0950A0] transition shadow-lg">
                                Daftar
                            </a>
                        @endif
                    </div>
                @endauth
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-200 hover:text-white hover:bg-white/10 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#0f2a4a] [text-shadow:none] border-t border-white/10">
        {{-- ... Isi Mobile Menu Anda ... --}}
        <div class="pt-2 pb-3 space-y-1">
            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                @if (Auth::user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.*')">
                        Panel Admin
                        @if ($pendingResetRequests)
                            <span class="ms-2 px-1.5 rounded-full bg-rose-500 text-white text-[11px] font-bold">{{ $pendingResetRequests }}</span>
                        @endif
                    </x-responsive-nav-link>
                @endif
            @endauth
            
            <x-responsive-nav-link :href="route('lpj.create')" :active="request()->routeIs('lpj.create')">
                {{ __('Buat LPJ Baru') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('fitur')">Fitur</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('cara-pakai')">Cara Pakai</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('bantuan')">Bantuan</x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-white/10">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-400">{{ '@'.Auth::user()->username }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        Profil
                    </x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            Keluar
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="mt-3 space-y-1 p-4">
                    <x-responsive-nav-link :href="route('login')">
                        Masuk
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')">
                        Daftar
                    </x-responsive-nav-link>
                </div>
            @endauth
        </div>
    </div>
</nav>