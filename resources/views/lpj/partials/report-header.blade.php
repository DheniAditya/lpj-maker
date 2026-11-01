<div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <x-lpj.inline-edit class="mb-6" :action="route('lpj.update-creator', $report->slug)"
            name="creator_name" :value="$report->creator_name" response-key="new_name">
            <span class="shrink-0 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-700 tracking-wider">Dibuat oleh:</span>
            <span data-value class="text-sm font-medium text-slate-800">{{ $report->creator_name }}</span>
        </x-lpj.inline-edit>

        <x-lpj.inline-edit class="mb-6" variant="title" :action="route('lpj.update-title', $report->slug)"
            name="title" :value="$report->title" response-key="new_title">
            <h1 data-value class="text-2xl font-bold text-slate-800">{{ $report->title }}</h1>
        </x-lpj.inline-edit>

        <div class="flex items-center gap-4 mt-2 text-sm">
            <div class="px-3 py-1 rounded-lg bg-slate-50 border border-slate-100">
                <span class="text-slate-400 text-xs uppercase font-bold mr-1">Sisa:</span>
                <span class="font-bold text-base {{ $report->balance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    @rupiah($report->balance)
                </span>
            </div>
        </div>
    </div>

    <a href="{{ route('lpj.download', $report->slug) }}" target="_blank"
        class="group bg-slate-800 text-white px-5 py-3 rounded-xl font-bold text-sm flex items-center hover:bg-slate-700 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
        <x-lpj.icon name="upload" class="w-5 h-5 mr-2 text-slate-400 group-hover:text-white transition" />
        <span>Download PDF</span>
    </a>
</div>
