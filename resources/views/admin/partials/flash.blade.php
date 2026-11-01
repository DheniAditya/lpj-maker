@if (session('status'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex gap-3">
        <x-lpj.icon name="check" class="w-5 h-5 shrink-0" /> {{ session('status') }}
    </div>
@endif
@if ($errors->any())
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm flex gap-3">
        <x-lpj.icon name="warning" class="w-5 h-5 shrink-0" /> {{ $errors->first() }}
    </div>
@endif
