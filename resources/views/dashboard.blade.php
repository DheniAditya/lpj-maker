<x-app-layout>
    <div class="">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sticky top-24">
                    
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">Buat Project</h2>
                            <p class="text-xs text-slate-500">Mulai laporan keuangan baru</p>
                        </div>
                    </div>
                    
                    <form action="{{ route('lpj.store') }}" method="POST">
                        @csrf
                        
                        <div class="space-y-4">
                            <div>
                                <label for="title" class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Judul Laporan</label>
                                <input type="text" name="title" id="title" 
                                       class="w-full bg-slate-50 border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-[#6B81A7] focus:border-[#6B81A7] transition p-3 placeholder-slate-400" 
                                       placeholder="Contoh: Dinas Luar Kota" required>
                            </div>
                            
                            <div>
                                <label for="creator_name" class="block text-xs font-bold text-slate-500 uppercase mb-1 ml-1">Penanggung Jawab</label>
                                <input type="text" name="creator_name" id="creator_name" 
                                       class="w-full bg-slate-50 border-slate-200 text-slate-700 text-sm rounded-xl focus:ring-[#6B81A7] focus:border-[#6B81A7] transition p-3 placeholder-slate-400" 
                                       placeholder="Nama Lengkap" required>
                            </div>
                        </div>

                        <button type="submit" class="w-full mt-6 bg-[#08254F] text-white p-3 rounded-xl font-bold hover:bg-[#0F346E] transition shadow-lg shadow-blue-900/20 flex justify-center items-center gap-2 group">
                            <span>Buat Laporan</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    
                    {{-- Header --}}
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <div>
                            <h2 class="text-lg font-bold text-slate-800">Arsip Laporan</h2>
                            <p class="text-xs text-slate-500">Daftar semua LPJ yang pernah Anda buat</p>
                        </div>
                        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                            {{ count($lpjHistory) }} File
                        </span>
                    </div>
                    
                    {{-- Content List --}}
                    <div class="p-6">
    <div class="grid gap-4">
        @forelse ($lpjHistory as $report)
            {{-- PERUBAHAN 1: Tambahkan ID unik di sini --}}
            <div id="row-{{ $report->id }}" class="group relative rounded-xl border border-slate-200 hover:border-[#6B81A7] hover:bg-blue-50/30 transition-all duration-200">
                
                <a href="{{ route('lpj.show', $report->slug) }}" class="block p-5 pr-16 w-full h-full">
                    <div class="flex justify-between items-start">
                        
                        {{-- Kiri: Icon & Info --}}
                        <div class="flex gap-4">
                            <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-white group-hover:text-[#6B81A7] transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            
                            <div>
                                <h3 class="font-bold text-slate-800 group-hover:text-[#08254F] transition text-lg mb-1">{{ $report->title }}</h3>
                                <div class="flex items-center gap-3 text-xs text-slate-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        {{ $report->creator_name }}
                                    </span>
                                    <span>•</span>
                                    <span>{{ $report->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Kanan: Saldo --}}
                        <div class="text-right">
                            <p class="text-xs text-slate-400 uppercase font-bold mb-1">Sisa Saldo</p>
                            <span class="font-mono font-bold text-lg {{ $report->balance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                Rp {{ number_format($report->balance, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </a>

                {{-- PERUBAHAN 2: Tombol Delete Baru (Tanpa Form) --}}
                <div class="absolute right-4 top-1/2 -translate-y-1/2 z-10">
                    <button type="button" 
                            class="btn-delete p-2 rounded-lg text-slate-300 hover:text-red-500 hover:bg-red-50 transition-colors"
                            data-id="{{ $report->id }}" 
                            data-title="{{ $report->title }}"
                            title="Hapus Laporan">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </div>

            </div>
        @empty
            <div class="text-center py-12 bg-slate-50 rounded-xl border border-dashed border-slate-300">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-slate-600 font-bold">Belum ada laporan</h3>
                <p class="text-slate-400 text-sm mt-1">Buat LPJ pertama Anda melalui formulir di sebelah kiri.</p>
            </div>
        @endforelse
    </div>
</div>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    $(document).ready(function() {
        
        // Setup Token Keamanan (Wajib untuk Laravel)
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Aksi ketika tombol hapus diklik
        $('.btn-delete').click(function(e) {
            e.preventDefault();

            // Ambil data dari tombol yang diklik
            let button = $(this);
            let id = button.data('id');
            let title = button.data('title');
            
            // Tampilkan SweetAlert
            Swal.fire({
                title: 'Hapus Laporan?',
                text: "Laporan '" + title + "' akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    
                    // Panggil Controller via AJAX (Tanpa Reload)
                    $.ajax({
                        type: "DELETE",
                        url: "/lpj/" + id, // Pastikan route ini sesuai
                        success: function(response) {
                            
                            // Animasi Baris Menghilang (Fade Out)
                            $('#row-' + id).fadeOut(600, function() {
                                $(this).remove(); 
                            });

                            // Pesan Sukses Kecil di Pojok (Toast)
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                            
                            Toast.fire({
                                icon: 'success',
                                title: 'Berhasil dihapus'
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
                        }
                    });
                }
            })
        });
    });
</script>