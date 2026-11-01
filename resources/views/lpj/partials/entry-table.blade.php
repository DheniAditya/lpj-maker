<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <h3 class="font-bold text-slate-700">Riwayat Transaksi</h3>
        <span class="text-xs font-bold bg-slate-200 text-slate-600 px-2 py-1 rounded">{{ $report->entries->count() }} Data</span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead class="bg-slate-50">
                <tr>
                    @foreach (['No' => 'text-center', 'Keterangan' => 'text-left', 'Nota' => 'text-center', 'Nominal' => 'text-right', 'Aksi' => 'text-center'] as $heading => $align)
                        <th class="p-4 {{ $align }} text-xs font-bold text-slate-400 uppercase tracking-wider">{{ $heading }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($report->entries as $entry)
                    @php
                        $editPayload = [
                            'type' => $entry->type,
                            'description' => $entry->description,
                            'amount' => (int) $entry->amount,
                            'date' => $entry->created_at->format('Y-m-d'),
                            'images' => $entry->images->map->only(['id', 'image_path']),
                        ];
                    @endphp
                    <tr id="entry-{{ $entry->id }}" class="hover:bg-blue-50/50 transition duration-150">
                        <td class="p-4 text-sm text-slate-600 text-center w-12">{{ $loop->iteration }}</td>
                        <td class="p-4 text-sm font-medium text-slate-800">{{ $entry->description }}</td>
                        <td class="p-4 text-center">
                            @if ($entry->images->isNotEmpty())
                                @include('lpj.partials.receipt-gallery', ['entry' => $entry])
                            @else
                                <span class="text-[10px] text-red-300">Tidak Ada</span>
                            @endif
                        </td>
                        <td class="p-4 text-right font-mono text-sm tracking-tight">
                            @if ($entry->isDebit())
                                <span class="text-emerald-600 bg-emerald-50 px-2 py-1 rounded font-bold">+ @angka($entry->amount)</span>
                            @else
                                <span class="text-rose-600 bg-rose-50 px-2 py-1 rounded font-bold">- @angka($entry->amount)</span>
                            @endif
                        </td>
                        <td class="p-4">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" class="btn-edit-entry p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-full transition" title="Edit Transaksi"
                                    data-entry="{{ json_encode($editPayload) }}"
                                    data-url="{{ route('lpj.entry.update', $entry) }}">
                                    <x-lpj.icon name="pencil" class="w-4 h-4" />
                                </button>
                                <button type="button" class="btn-delete-entry p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-full transition" title="Hapus Transaksi"
                                    data-url="{{ route('lpj.entry.destroy', $entry) }}" data-row="#entry-{{ $entry->id }}">
                                    <x-lpj.icon name="trash" class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400">Belum ada transaksi</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
