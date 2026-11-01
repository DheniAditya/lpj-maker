{{-- Pilihan Debit / Kredit. $prefix membedakan id antara form tambah dan modal edit. --}}
@php($prefix = $prefix ?? '')
<div class="grid grid-cols-2 gap-3 mb-4">
    @foreach ([
        'debit' => ['Debit', 'Masuk', 'peer-checked:bg-emerald-500 peer-checked:border-emerald-600'],
        'credit' => ['Kredit', 'Keluar', 'peer-checked:bg-rose-500 peer-checked:border-rose-600'],
    ] as $type => [$label, $hint, $checkedClass])
        <div>
            <input type="radio" name="type" value="{{ $type }}" id="{{ $prefix }}{{ $type }}" class="hidden peer" @checked(($checked ?? null) === $type)>
            <label for="{{ $prefix }}{{ $type }}"
                class="block w-full p-3 text-center border-2 border-slate-100 bg-slate-50 rounded-xl cursor-pointer peer-checked:text-white {{ $checkedClass }} transition-all hover:bg-white">
                <span class="block font-bold text-sm">{{ $label }}</span>
                <span class="text-[10px] opacity-80 uppercase">{{ $hint }}</span>
            </label>
        </div>
    @endforeach
</div>
