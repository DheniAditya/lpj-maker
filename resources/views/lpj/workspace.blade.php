<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8 px-4">
        @guest
            @include('lpj.partials.guest-notice')
        @endguest

        @include('lpj.partials.report-header')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <div class="col-span-1 lg:sticky top-8 z-30">
                @include('lpj.partials.entry-form')
            </div>

            <div class="col-span-1 lg:col-span-2">
                @include('lpj.partials.entry-table')
            </div>
        </div>
    </div>

    {{-- Tombol melayang --}}
    <div class="fixed bottom-6 right-6 z-40">
        @auth
            <a href="{{ route('dashboard') }}" class="bg-slate-800 text-white px-6 py-3 rounded-full font-bold shadow-2xl hover:bg-slate-700 transition flex items-center gap-2 border border-slate-600">
                <x-lpj.icon name="arrow-left" /> Dashboard
            </a>
        @else
            <a href="{{ route('lpj.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-full font-bold shadow-2xl hover:bg-blue-500 transition flex items-center gap-2 hover:-translate-y-1 transform">
                <x-lpj.icon name="plus" /> Buat Baru
            </a>
        @endauth
    </div>

    @include('lpj.partials.edit-entry-modal')

    @push('scripts')
        @include('lpj.partials.workspace-scripts')
    @endpush
</x-app-layout>
