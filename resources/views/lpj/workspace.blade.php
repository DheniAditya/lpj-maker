<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-2 pb-8">
        {{-- Navigasi atas (pengganti tombol melayang yang menutupi kamera di HP) --}}
        <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
            @auth
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-[13px] font-bold text-white/90 hover:text-white bg-white/10 hover:bg-white/20 px-3.5 py-1 min-h-[32px] rounded-full transition">
                    <x-lpj.icon name="arrow-left" class="w-4 h-4" /> Dashboard
                </a>
            @else
                <a href="{{ route('lpj.create') }}" class="inline-flex items-center gap-2 text-[13px] font-bold text-white/90 hover:text-white bg-white/10 hover:bg-white/20 px-3.5 py-1 min-h-[32px] rounded-full transition">
                    <x-lpj.icon name="plus" class="w-4 h-4" /> Buat LPJ Baru
                </a>
            @endauth

            @can('share', $report)
                <x-lpj.share-button :report="$report" class="text-[13px] font-bold text-white/90 hover:text-white bg-white/10 hover:bg-white/20 px-3.5 py-1 min-h-[32px] rounded-full">
                    Bagikan
                </x-lpj.share-button>
            @endcan
        </div>

        @guest
            @include('lpj.partials.guest-notice')
        @endguest

        @if (session('error'))
            <div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex gap-3">
                <x-lpj.icon name="warning" class="w-5 h-5 shrink-0" /> {{ session('error') }}
            </div>
        @endif

        @include('lpj.partials.report-header')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 items-start">
            {{-- Layar kamera di-teleport ke <body> agar tidak tertutup navbar --}}
            <div class="col-span-1 lg:sticky lg:top-24">
                @can('update', $report)
                    @include('lpj.partials.entry-form')
                @else
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 text-sm text-slate-500 flex gap-3">
                        <x-lpj.icon name="eye" class="w-5 h-5 shrink-0" />
                        Anda membuka LPJ ini sebagai <b>pelihat</b>. Transaksi hanya bisa ditambah atau diubah oleh pembuat/editor.
                    </div>
                @endcan
            </div>

            <div class="col-span-1 lg:col-span-2">
                @include('lpj.partials.entry-table')
            </div>
        </div>
    </div>

    @can('update', $report)
        @include('lpj.partials.edit-entry-modal')
    @endcan

    @can('share', $report)
        <x-lpj.share-modal />
    @endcan

    @push('scripts')
        @include('lpj.partials.workspace-scripts')
    @endpush
</x-app-layout>
