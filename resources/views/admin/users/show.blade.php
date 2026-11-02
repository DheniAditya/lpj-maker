<x-app-layout>
    @php($isSelf = $user->is(auth()->user()))

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6 space-y-6">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-[13px] font-bold text-white hover:text-white bg-[#08254F]/60 hover:bg-[#08254F]/80 backdrop-blur-sm px-3.5 py-1 min-h-[32px] rounded-full transition">
            <x-lpj.icon name="arrow-left" class="w-4 h-4" /> Semua pengguna
        </a>

        @include('admin.partials.flash')

        @if ($resetRequest)
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex gap-3">
                <x-lpj.icon name="key" class="w-5 h-5 shrink-0" />
                <div>
                    <b>{{ $user->name }} meminta reset password</b> ({{ $resetRequest->updated_at->diffForHumans() }}).
                    @if ($resetRequest->contact) Hubungi via WA: <b>{{ $resetRequest->contact }}</b>. @endif
                    @if ($resetRequest->note) <span class="italic">“{{ $resetRequest->note }}”</span> @endif
                    Buat password baru di bawah, lalu sampaikan ke pengguna.
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <div class="space-y-6">
                {{-- Profil --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-5">
                    <h2 class="font-bold text-slate-800 mb-4 flex items-center gap-2"><x-lpj.icon name="user" /> Data akun</h2>
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-3">
                        @csrf @method('PATCH')
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama</label>
                            <input name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Username</label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}" required autocapitalize="none" spellcheck="false" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm font-mono">
                        </div>
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input type="hidden" name="is_admin" value="0">
                            <input type="checkbox" name="is_admin" value="1" @checked($user->isAdmin()) @disabled($isSelf) class="rounded border-slate-300 text-blue-600">
                            Jadikan admin
                            @if ($isSelf) <span class="text-[11px] text-slate-400">(akun Anda)</span> @endif
                        </label>
                        @if ($isSelf) <input type="hidden" name="is_admin" value="1"> @endif
                        <p class="text-[11px] text-slate-400">Terdaftar {{ $user->created_at->translatedFormat('d M Y H:i') }}</p>
                        <button class="w-full py-2.5 rounded-xl bg-slate-800 text-white text-sm font-bold hover:bg-slate-700">Simpan</button>
                    </form>
                </div>

                {{-- Reset password --}}
                <div id="reset-password" class="bg-white rounded-2xl border {{ $resetRequest ? 'border-rose-300 ring-4 ring-rose-100' : 'border-slate-200' }} p-5 scroll-mt-24">
                    <h2 class="font-bold text-slate-800 mb-1 flex items-center gap-2"><x-lpj.icon name="key" /> Reset password</h2>
                    <p class="text-xs text-slate-500 mb-4">
                        Password disimpan terenkripsi (hash), jadi tidak bisa dilihat siapa pun, termasuk admin.
                        Untuk pengguna yang lupa, buatkan password baru lalu sampaikan ke pengguna.
                    </p>
                    <form method="POST" action="{{ route('admin.users.password', $user) }}" class="space-y-3" x-data="{ show: false }">
                        @csrf @method('PUT')
                        <input :type="show ? 'text' : 'password'" name="password" required minlength="8" placeholder="Password baru (min. 8 karakter)" autocomplete="new-password" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm">
                        <input :type="show ? 'text' : 'password'" name="password_confirmation" required placeholder="Ulangi password baru" autocomplete="new-password" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm">
                        <label class="flex items-center gap-2 text-xs text-slate-500"><input type="checkbox" x-model="show" class="rounded border-slate-300"> Tampilkan yang saya ketik</label>
                        <button class="w-full py-2.5 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700">Ganti password</button>
                    </form>

                </div>

                {{-- Hapus --}}
                @unless ($isSelf)
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                        onsubmit="return confirm('Hapus akun {{ addslashes($user->name) }} beserta {{ $reports->count() }} LPJ-nya? Tindakan ini tidak bisa dibatalkan.')">
                        @csrf @method('DELETE')
                        <button class="w-full inline-flex items-center justify-center gap-2 py-2.5 rounded-xl bg-rose-50 text-rose-600 text-sm font-bold hover:bg-rose-100">
                            <x-lpj.icon name="trash" class="w-4 h-4" /> Hapus akun & semua LPJ
                        </button>
                    </form>
                @endunless
            </div>

            {{-- LPJ milik pengguna --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="font-bold text-slate-800 flex items-center gap-2"><x-lpj.icon name="document" /> LPJ milik {{ $user->name }}</h2>
                    <span class="text-xs font-bold bg-slate-100 text-slate-600 px-2 py-1 rounded">{{ $reports->count() }}{{ $user->isAdmin() ? '' : '/'.\App\Models\User::MAX_REPORTS }}</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($reports as $report)
                        <div class="p-4 sm:px-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-800 break-words">{{ $report->title }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $report->creator_name }} · {{ $report->entries_count }} transaksi · diubah {{ $report->updated_at->diffForHumans() }}
                                    · {{ $report->signature_path ? 'Sudah TTD' : 'Belum TTD' }}
                                    @if ($report->is_shared) · Dibagikan ({{ $report->share_role === 'editor' ? 'Editor' : 'Pelihat' }}) @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="font-mono font-bold {{ $report->balance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">@rupiah($report->balance)</span>
                                <a href="{{ route('lpj.show', $report->slug) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100">
                                    <x-lpj.icon name="external" class="w-4 h-4" /> Buka
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="p-8 text-center text-slate-400 text-sm">Pengguna ini belum membuat LPJ.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
