@props(['disabled' => false])

{{-- Fokus: garis tipis warna aplikasi (bukan cincin ungu tebal bawaan Breeze) --}}
<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-[#6B81A7] focus:ring-1 focus:ring-[#6B81A7] rounded-md shadow-sm']) }}>
