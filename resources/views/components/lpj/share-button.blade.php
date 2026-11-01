{{-- Tombol pembuka modal "Bagikan LPJ" (hanya untuk pemilik). --}}
@props(['report', 'iconClass' => 'w-4 h-4'])

@php
    $detail = [
        'title' => $report->title,
        'token' => $report->share_token,
        'role' => $report->share_role ?? 'viewer',
        'shared' => (bool) $report->is_shared,
        'updateUrl' => route('lpj.share.update', $report),
        'revokeUrl' => route('lpj.share.revoke', $report),
    ];
@endphp

<button type="button" x-data @click="$dispatch('open-share-modal', {{ Illuminate\Support\Js::from($detail) }})"
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 transition']) }}>
    <x-lpj.icon name="share" :class="$iconClass" />
    <span>{{ $slot->isEmpty() ? 'Bagikan' : $slot }}</span>
</button>
