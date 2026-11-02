<x-guest-layout>
    
    <div class="bg-white w-full max-w-lg mx-auto p-8 sm:p-10 rounded-[2.5rem] shadow-2xl relative">
        
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Masuk ke akun kamu</h2>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            {{-- INPUT USERNAME --}}
            <div>
                <x-input-label for="username" value="Username" class="block text-gray-600 text-sm font-bold mb-2 ml-1 tracking-wide" />
                <x-text-input id="username"
                              type="text"
                              name="username"
                              :value="old('username')"
                              required autofocus autocomplete="username" autocapitalize="none" spellcheck="false"
                              placeholder="Masukkan username"
                              class="w-full py-4 px-5 bg-gray-50 rounded-2xl border-gray-200 text-gray-700 placeholder-gray-400 shadow-sm transition-all focus:ring-1 focus:ring-[#6B81A7] focus:border-[#6B81A7] focus:bg-white focus:outline-none" />
                <x-input-error :messages="$errors->get('username')" class="mt-2 text-xs text-red-500 font-medium ml-1" />
            </div>

            {{-- INPUT PASSWORD --}}
            <div>
                <x-input-label for="password" :value="__('Password')" class="block text-gray-600 text-sm font-bold mb-2 ml-1 tracking-wide" />
                <x-text-input id="password" 
                              type="password" 
                              name="password" 
                              required autocomplete="current-password" 
                              placeholder="Masukkan password"
                              class="w-full py-4 px-5 bg-gray-50 rounded-2xl border-gray-200 text-gray-700 placeholder-gray-400 shadow-sm transition-all focus:ring-1 focus:ring-[#6B81A7] focus:border-[#6B81A7] focus:bg-white focus:outline-none" />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-500 font-medium ml-1" />
            </div>

            {{-- CHECKBOX REMEMBER ME --}}
            <div class="flex items-center justify-between px-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer group select-none relative">
                    
                    {{-- Checkbox Asli (Tanpa wrapper induk, tidak butuh !important) --}}
                    <input id="remember_me" type="checkbox" 
                           class="peer appearance-none w-5 h-5 rounded bg-white border border-gray-300 text-[#6B81A7] shadow-sm checked:bg-[#6B81A7] checked:border-transparent focus:ring-2 focus:ring-[#6B81A7] focus:ring-offset-0 cursor-pointer transition-all" 
                           name="remember" checked>
                    
                    {{-- Icon Centang SVG --}}
                    <svg class="absolute left-0 top-0 w-5 h-5 hidden peer-checked:block pointer-events-none text-white" 
                         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                         <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>

                    <span class="ml-2 text-sm text-gray-500 group-hover:text-[#6B81A7] transition-colors duration-200">
                        Ingat saya
                    </span>
                </label>

                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-400 hover:text-[#6B81A7] transition rounded-md" href="{{ route('password.request') }}">
                        Lupa password?
                    </a>
                @endif
            </div>

            {{-- TOMBOL LOGIN --}}
            <x-primary-button class="w-full justify-center py-4 text-lg font-bold rounded-2xl bg-[#4A5D7B] hover:bg-[#36445a] shadow-lg hover:shadow-xl transition-all active:scale-[0.98] hover:-translate-y-0.5">
                Masuk
            </x-primary-button>
            
            {{-- REGISTER LINK --}}
            <div class="mt-6 text-center text-sm text-gray-500">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-bold text-[#6B81A7] hover:underline hover:text-[#4A5D7B] transition">Daftar</a>
            </div>

        </form>
    </div>

</x-guest-layout>