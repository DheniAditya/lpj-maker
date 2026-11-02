@php
    $steps = [
        ['Masuk atau Daftar', 'Punya akun? Masuk dengan username. Belum punya? Daftar dulu, atau langsung buat LPJ dalam Mode Tamu.', ['Masuk', 'Daftar']],
        ['Buat LPJ Baru', 'Isi Judul Dokumen dan Atas Nama (Penanggung Jawab), lalu tekan Buat.', ['Buat']],
        ['Tambah Transaksi', 'Pilih Debit (Masuk) atau Kredit (Keluar), isi Keterangan dan Nominal, lalu lampirkan Bukti Transaksi lewat Upload atau Kamera.', ['Simpan Transaksi']],
        ['Tanda Tangan', 'Tekan Tanda Tangan, pilih Tulis atau Gambar, lalu Simpan Tanda Tangan. Download PDF terbuka setelah ditandatangani dan semua transaksi Kredit memiliki nota.', ['Tanda Tangan']],
        ['Download PDF atau Bagikan', 'Unduh LPJ siap cetak, atau Bagikan link ke rekan sebagai Pelihat maupun Editor.', ['Download PDF', 'Bagikan']],
    ];
@endphp
<x-info-layout title="Cara Pakai" kicker="Cara Pakai" heading="5 langkah dari nota<br class='hidden sm:block'> sampai PDF"
               sub="Bisa dikerjakan dari HP atau laptop.">

    <div class="grid gap-8 lg:grid-cols-[1.4fr_1fr] items-start">
        <ol class="flex flex-col gap-4">
            @foreach ($steps as $i => [$name, $desc, $tags])
                <li class="bg-white border border-slate-200 rounded-[20px] p-5 sm:p-6 flex gap-4">
                    <span class="shrink-0 w-10 h-10 rounded-full bg-[#0B6BD3] text-white font-extrabold flex items-center justify-center">{{ $i + 1 }}</span>
                    <div class="min-w-0">
                        <h3 class="text-lg font-extrabold text-[#0f2a4a]">{{ $name }}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-slate-600">{{ $desc }}</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($tags as $tag)
                                <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-bold">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                </li>
            @endforeach
        </ol>

        <aside class="flex flex-col gap-4">
            <div class="bg-white border border-slate-200 rounded-3xl p-7">
                <h2 class="text-xl font-extrabold text-[#0f2a4a]">Mode Tamu</h2>
                <p class="mt-2.5 text-sm leading-relaxed text-slate-600">Anda berada di mode tamu. Riwayat pekerjaan anda akan terhapus otomatis setelah halaman ini di tutup. Silahkan download LPJ anda sebelum menutup halaman.</p>
                <p class="mt-3 text-sm text-slate-600">
                    <a href="{{ route('register') }}" class="font-bold text-[#0B6BD3] underline">Daftar Akun</a> atau
                    <a href="{{ route('login') }}" class="font-bold text-[#0B6BD3] underline">Login</a> untuk menyimpan riwayat pekerjaan anda.
                </p>
            </div>
            <div class="bg-[#0f2a4a] text-white rounded-3xl p-7">
                <h2 class="text-xl font-extrabold">Siap mulai?</h2>
                <p class="mt-2 text-sm text-white/85">Mulai laporan pertanggungjawaban anda di sini</p>
                <div class="mt-4 flex flex-wrap gap-2.5">
                    <a href="{{ route('lpj.create') }}" class="inline-flex items-center min-h-[44px] px-5 rounded-xl bg-white text-[#0f172a] text-sm font-bold hover:bg-slate-100 transition">Buat LPJ Baru</a>
                    <a href="{{ route('bantuan') }}" class="inline-flex items-center min-h-[44px] px-5 rounded-xl bg-[#0B6BD3] text-white text-sm font-bold hover:bg-[#0950A0] transition">Bantuan</a>
                </div>
            </div>
        </aside>
    </div>
</x-info-layout>
