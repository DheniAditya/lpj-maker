<x-app-layout>
    @php
        $user = auth()->user();
        $limit = \App\Models\User::MAX_REPORTS;
        $isFull = $user->hasReachedReportLimit();
    @endphp

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6 grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">

        {{-- Buat LPJ --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 lg:sticky lg:top-24">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <x-lpj.icon name="document-plus" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">Buat LPJ</h2>
                        <p class="text-xs text-slate-500">Mulai laporan keuangan baru</p>
                    </div>
                </div>

                @if ($isFull)
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm flex gap-3">
                        <x-lpj.icon name="warning" class="w-5 h-5 shrink-0" />
                        <p>Arsip sudah penuh ({{ $limit }} LPJ). Hapus LPJ lama yang sudah tidak dipakai untuk membuat yang baru.</p>
                    </div>
                @else
                    <form action="{{ route('lpj.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="title" class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Judul Laporan</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="Contoh: Dinas Luar Kota"
                                class="w-full bg-slate-50 border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-[#6B81A7] focus:border-[#6B81A7] transition p-3 placeholder-slate-400">
                        </div>
                        <div>
                            <label for="creator_name" class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Penanggung Jawab</label>
                            <input type="text" name="creator_name" id="creator_name" value="{{ old('creator_name', $user->name) }}" required placeholder="Nama Lengkap"
                                class="w-full bg-slate-50 border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-[#6B81A7] focus:border-[#6B81A7] transition p-3 placeholder-slate-400">
                        </div>
                        <x-input-error :messages="$errors->get('title')" />

                        <button type="submit" class="w-full bg-[#08254F] text-white p-3 rounded-xl font-bold hover:bg-[#0F346E] transition shadow-lg shadow-blue-900/20 flex justify-center items-center gap-2">
                            <x-lpj.icon name="plus" class="w-4 h-4" /> Buat Laporan
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Arsip --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-slate-100 flex justify-between items-center gap-4 bg-slate-50/50">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">Arsip Laporan</h2>
                        <p class="text-xs text-slate-500">Daftar LPJ yang pernah Anda buat</p>
                    </div>
                    <span @class([
                        'text-xs font-bold px-3 py-1 rounded-full whitespace-nowrap',
                        'bg-amber-100 text-amber-700' => $isFull,
                        'bg-blue-100 text-blue-700' => ! $isFull,
                    ])>
                        {{ $lpjHistory->count() }}{{ $user->isAdmin() ? '' : '/'.$limit }} Arsip
                    </span>
                </div>

                <div class="p-4 sm:p-6 grid gap-4">
                    @forelse ($lpjHistory as $report)
                        <div id="row-{{ $report->id }}" class="rounded-xl border border-slate-200 hover:border-[#6B81A7] transition overflow-hidden">
                            <a href="{{ route('lpj.show', $report->slug) }}" class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 p-4 sm:p-5 hover:bg-blue-50/30 transition">
                                <div class="flex items-start gap-4 min-w-0 flex-1">
                                    <div class="w-11 h-11 shrink-0 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400">
                                        <x-lpj.icon name="document" />
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-bold text-slate-800 text-base sm:text-lg leading-snug break-words">{{ $report->title }}</h3>
                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-slate-500 mt-1">
                                            <span class="inline-flex items-center gap-1"><x-lpj.icon name="user" class="w-3.5 h-3.5" /> {{ $report->creator_name }}</span>
                                            <span>{{ $report->updated_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="sm:text-right pl-[3.75rem] sm:pl-0 shrink-0">
                                    <p class="text-[10px] text-slate-400 uppercase font-bold">Sisa Saldo</p>
                                    <span class="font-mono font-bold text-lg {{ $report->balance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">@rupiah($report->balance)</span>
                                </div>
                            </a>

                            {{-- Status & aksi: dipisah dari info saldo agar tidak bertabrakan --}}
                            <div class="flex flex-wrap items-center justify-between gap-2 px-4 sm:px-5 py-2.5 border-t border-slate-100 bg-slate-50/70">
                                <div class="flex flex-wrap gap-1.5 text-[10px] font-semibold leading-none">
                                    @if ($report->is_shared)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-emerald-100 text-emerald-700">
                                            <x-lpj.icon name="link" class="w-3.5 h-3.5" /> Dibagikan · {{ $report->share_role === 'editor' ? 'Editor' : 'Pelihat' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-200 text-slate-600">
                                            <x-lpj.icon name="lock" class="w-3.5 h-3.5" /> Privat
                                        </span>
                                    @endif
                                    @if ($report->signature_path)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-blue-100 text-blue-700">
                                            <x-lpj.icon name="signature" class="w-3.5 h-3.5" /> Sudah TTD
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-amber-100 text-amber-700">
                                            <x-lpj.icon name="signature" class="w-3.5 h-3.5" /> Belum TTD
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1">
                                    <x-lpj.share-button :report="$report" icon-class="w-3.5 h-3.5" class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-slate-600 hover:text-blue-700 hover:bg-blue-50" />
                                    <button type="button" class="btn-delete inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-bold text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition"
                                        data-url="{{ route('lpj.destroy', $report) }}" data-row="#row-{{ $report->id }}" data-title="{{ $report->title }}">
                                        <x-lpj.icon name="trash" class="w-3.5 h-3.5" /> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400">
                                <x-lpj.icon name="document-plus" class="w-8 h-8" />
                            </div>
                            <h3 class="text-slate-600 font-bold">Belum ada laporan</h3>
                            <p class="text-slate-400 text-sm mt-1">Buat LPJ pertama Anda melalui formulir di samping.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <x-lpj.share-modal />

    @push('scripts')
        <script>
            $(function () {
                $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

                $('.btn-delete').on('click', function () {
                    const $btn = $(this);

                    Swal.fire({
                        title: 'Hapus Laporan?',
                        text: `Laporan '${$btn.data('title')}' beserta semua nota dan tanda tangannya akan dihapus permanen.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                    }).then(({ isConfirmed }) => {
                        if (!isConfirmed) return;

                        $.ajax({ type: 'DELETE', url: $btn.data('url') })
                            .done(() => {
                                Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 })
                                    .fire({ icon: 'success', title: 'Berhasil dihapus' });
                                setTimeout(() => location.reload(), 900); // perbarui hitungan arsip
                            })
                            .fail(() => Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error'));
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
