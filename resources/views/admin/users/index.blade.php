<x-app-layout>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6 space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3 text-white">
                <div class="w-11 h-11 rounded-xl bg-white/15 flex items-center justify-center"><x-lpj.icon name="shield" /></div>
                <div>
                    <h1 class="text-xl font-bold">Panel Admin</h1>
                    <p class="text-xs text-white/70">Pantau dan kelola pengguna LPJ Maker</p>
                </div>
            </div>
        </div>

        @include('admin.partials.flash')

        {{-- Statistik --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach ([
                ['users', 'Pengguna', $stats['users']],
                ['document', 'Total LPJ', $stats['reports']],
                ['user', 'LPJ mode tamu', $stats['guestReports']],
                ['link', 'LPJ dibagikan', $stats['sharedReports']],
            ] as [$icon, $label, $value])
                <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0"><x-lpj.icon :name="$icon" /></div>
                    <div>
                        <p class="text-2xl font-bold text-slate-800 leading-none">{{ number_format($value, 0, ',', '.') }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $label }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Daftar pengguna --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <h2 class="font-bold text-slate-800">Pengguna</h2>
                <form method="GET" class="relative w-full sm:w-72">
                    <x-lpj.icon name="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                    <input type="search" name="q" value="{{ $search }}" placeholder="Cari nama atau email..."
                        class="w-full pl-9 pr-3 py-2 text-sm rounded-xl border-slate-200 bg-slate-50 focus:bg-white">
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-400">
                        <tr>
                            <th class="p-4 text-left font-bold">Nama</th>
                            <th class="p-4 text-left font-bold">Email</th>
                            <th class="p-4 text-center font-bold">LPJ</th>
                            <th class="p-4 text-left font-bold">Terdaftar</th>
                            <th class="p-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $user)
                            <tr class="hover:bg-slate-50">
                                <td class="p-4">
                                    <div class="font-semibold text-slate-800">{{ $user->name }}</div>
                                    @if ($user->isAdmin())
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold uppercase text-blue-700 bg-blue-100 px-1.5 py-0.5 rounded mt-1"><x-lpj.icon name="shield" class="w-3 h-3" /> Admin</span>
                                    @endif
                                </td>
                                <td class="p-4 text-slate-600">
                                    {{ $user->email }}
                                    @unless ($user->email_verified_at)
                                        <span class="block text-[11px] text-amber-600">Email belum diverifikasi</span>
                                    @endunless
                                </td>
                                <td class="p-4 text-center">
                                    <span class="font-bold {{ $user->expense_reports_count >= \App\Models\User::MAX_REPORTS && ! $user->isAdmin() ? 'text-amber-600' : 'text-slate-700' }}">{{ $user->expense_reports_count }}</span>
                                </td>
                                <td class="p-4 text-slate-500 whitespace-nowrap">{{ $user->created_at->translatedFormat('d M Y') }}</td>
                                <td class="p-4 text-right">
                                    <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100">
                                        <x-lpj.icon name="eye" class="w-4 h-4" /> Kelola
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-8 text-center text-slate-400">Tidak ada pengguna yang cocok.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="p-4 border-t border-slate-100">{{ $users->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
