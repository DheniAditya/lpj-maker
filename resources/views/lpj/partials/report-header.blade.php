@php
    $canEdit = Gate::allows('update', $report);
    $hasSignature = $report->hasSignature();
    $missingReceipts = $report->creditNumbersMissingReceipt();
    $canDownload = $hasSignature && ! $missingReceipts;
    $lockReason = ! $hasSignature ? 'Tambahkan tanda tangan terlebih dahulu' : 'Nota belum lengkap';
@endphp

<div class="bg-white p-5 sm:p-6 rounded-2xl shadow-sm border border-slate-200 mb-6 sm:mb-8 flex flex-col lg:flex-row justify-between lg:items-center gap-6">
    {{-- Info LPJ --}}
    <div class="min-w-0">
        @if ($canEdit)
            <x-lpj.inline-edit class="mb-4" :action="route('lpj.update-creator', $report->slug)"
                name="creator_name" :value="$report->creator_name" response-key="new_name">
                <span class="shrink-0 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-700 tracking-wider">Dibuat oleh:</span>
                <span data-value class="text-sm font-medium text-slate-800">{{ $report->creator_name }}</span>
            </x-lpj.inline-edit>

            <x-lpj.inline-edit class="mb-4" variant="title" :action="route('lpj.update-title', $report->slug)"
                name="title" :value="$report->title" response-key="new_title">
                <h1 data-value class="text-xl sm:text-2xl font-bold text-slate-800 break-words">{{ $report->title }}</h1>
            </x-lpj.inline-edit>
        @else
            <div class="flex items-center gap-3 mb-4">
                <span class="shrink-0 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-700 tracking-wider">Dibuat oleh:</span>
                <span class="text-sm font-medium text-slate-800">{{ $report->creator_name }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 break-words mb-4">{{ $report->title }}</h1>
        @endif

        <div class="inline-flex items-center px-3 py-1 rounded-lg bg-slate-50 border border-slate-100">
            <span class="text-slate-400 text-xs uppercase font-bold mr-1">Sisa:</span>
            <span class="font-bold text-base {{ $report->balance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">@rupiah($report->balance)</span>
        </div>
    </div>

    {{-- Aksi: Tanda tangan → Download --}}
    <div class="flex flex-col gap-3 lg:items-end shrink-0" x-data="signaturePad(@js(['saveUrl' => route('lpj.signature.store', $report->slug), 'deleteUrl' => route('lpj.signature.destroy', $report->slug)]))">
        <div class="grid grid-cols-2 sm:flex gap-2">
            @if ($canEdit)
                <button type="button" @click="show()"
                    @class([
                        'inline-flex items-center justify-center gap-2 px-4 sm:px-5 py-3 rounded-xl font-bold text-sm transition border-2',
                        'border-slate-200 text-slate-700 hover:bg-slate-50' => $hasSignature,
                        'border-blue-600 text-blue-700 bg-blue-50 hover:bg-blue-100' => ! $hasSignature,
                    ])>
                    <x-lpj.icon name="signature" />
                    <span>{{ $hasSignature ? 'Ganti TTD' : 'Tanda Tangan' }}</span>
                </button>
            @endif

            @if ($canDownload)
                <a href="{{ route('lpj.download', $report->slug) }}" target="_blank"
                    class="inline-flex items-center justify-center gap-2 bg-slate-800 text-white px-4 sm:px-5 py-3 rounded-xl font-bold text-sm hover:bg-slate-700 transition shadow-lg {{ $canEdit ? '' : 'col-span-2' }}">
                    <x-lpj.icon name="download" />
                    <span>Download PDF</span>
                </a>
            @else
                <button type="button" disabled title="{{ $lockReason }}"
                    class="inline-flex items-center justify-center gap-2 bg-slate-200 text-slate-400 px-4 sm:px-5 py-3 rounded-xl font-bold text-sm cursor-not-allowed {{ $canEdit ? '' : 'col-span-2' }}">
                    <x-lpj.icon name="lock" />
                    <span>Download PDF</span>
                </button>
            @endif
        </div>

        @if ($hasSignature)
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold"><x-lpj.icon name="check" class="w-4 h-4" /> Sudah ditandatangani</span>
                <img src="{{ $report->signatureUrl() }}" alt="Tanda tangan" class="h-8 max-w-[120px] object-contain bg-slate-50 rounded border border-slate-100 px-1">
            </div>
        @else
            <p class="text-xs text-amber-600 font-medium inline-flex items-center gap-1">
                <x-lpj.icon name="warning" class="w-4 h-4" />
                {{ $canEdit ? 'Tambahkan tanda tangan untuk bisa mengunduh PDF.' : 'Menunggu tanda tangan dari pembuat LPJ.' }}
            </p>
        @endif

        @if ($missingReceipts)
            <p data-missing-receipts class="text-xs text-amber-600 font-medium inline-flex items-start gap-1 lg:text-right">
                <x-lpj.icon name="warning" class="w-4 h-4 shrink-0" />
                <span>Nota belum lengkap. Anda belum bisa mengunduh pdf</span>
            </p>
        @endif

        @if ($canEdit)
            @include('lpj.partials.signature-modal')
        @endif
    </div>
</div>
