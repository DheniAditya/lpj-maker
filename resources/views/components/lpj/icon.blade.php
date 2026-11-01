{{-- Ikon garis (stroke) yang dipakai berulang di halaman LPJ. Pemakaian: <x-lpj.icon name="pencil" class="w-5 h-5" /> --}}
@props(['name', 'strokeWidth' => 2])

@php
    $paths = [
        'pencil' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
        'close' => 'M6 18L18 6M6 6l12 12',
        'upload' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12',
        'camera' => 'M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z',
        'switch-camera' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
        'image' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
        'trash' => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
        'check' => 'M5 13l4 4L19 7',
        'arrow-left' => 'M10 19l-7-7m0 0l7-7m-7 7h18',
        'plus' => 'M12 4v16m8-8H4',
    ];
@endphp

<svg {{ $attributes->merge(['class' => 'w-5 h-5']) }} fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $strokeWidth }}" d="{{ $paths[$name] }}" />
    @if ($name === 'camera')
        <circle cx="12" cy="13" r="3" stroke-width="{{ $strokeWidth }}" />
    @endif
</svg>
