@php
    $features = [
        ['plus', 'bg-sky-100 text-sky-700', 'Tambah Transaksi', 'Catat Debit (Masuk) dan Kredit (Keluar) dengan keterangan dan nominal. Saldo dihitung otomatis.'],
        ['camera', 'bg-amber-100 text-amber-700', 'Bukti Transaksi', 'Upload foto nota atau langsung pakai Kamera dari HP. Semua nota ikut terlampir di PDF.'],
        ['signature', 'bg-violet-100 text-violet-700', 'Tanda Tangan', 'Tulis langsung di layar atau pilih Gambar. Latar putih otomatis dibuat transparan.'],
        ['download', 'bg-green-100 text-green-700', 'Download PDF', 'LPJ siap cetak: tabel bernomor, total mutasi, saldo akhir, tanda tangan, dan lampiran nota.'],
        ['share', 'bg-blue-100 text-blue-700', 'Bagikan', 'Bagikan link sebagai Pelihat atau Editor, kirim lewat WhatsApp, dan batalkan link share kapan saja.'],
        ['document', 'bg-slate-100 text-slate-700', 'Arsip Laporan', 'Masuk untuk menyimpan sampai '.\App\Models\User::MAX_REPORTS.' LPJ. Tanpa akun tetap bisa membuat LPJ lewat Mode Tamu.'],
    ];
@endphp
<x-info-layout title="Fitur" kicker="Fitur" heading="Semua yang dibutuhkan<br class='hidden sm:block'> untuk LPJ yang rapi"
               sub="Dari mencatat transaksi sampai PDF bertanda tangan, semuanya dari satu halaman.">

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($features as [$icon, $tone, $name, $desc])
            <article class="bg-white border border-slate-200 rounded-[20px] p-6">
                <span class="w-12 h-12 rounded-xl flex items-center justify-center {{ $tone }}"><x-lpj.icon :name="$icon" class="w-6 h-6" /></span>
                <h3 class="mt-4 text-lg font-extrabold text-[#0f2a4a]">{{ $name }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $desc }}</p>
            </article>
        @endforeach
    </div>

    <div class="mt-10 p-7 sm:p-8 rounded-3xl bg-[#0f2a4a] text-white flex flex-wrap items-center justify-between gap-5">
        <div>
            <p class="text-xl sm:text-[22px] font-extrabold">SMP SMK IDN Solo</p>
            <p class="mt-1.5 text-[13px] font-bold tracking-[0.12em] uppercase text-[#BFDBFE]">Jagoan IT Pinter Ngaji</p>
        </div>
        <div class="flex flex-wrap gap-2.5">
            <a href="{{ route('lpj.create') }}" class="inline-flex items-center min-h-[44px] px-5 rounded-xl bg-white text-[#0f172a] text-sm font-bold hover:bg-slate-100 transition">Buat LPJ Baru</a>
            @guest
                <a href="{{ route('register') }}" class="inline-flex items-center min-h-[44px] px-5 rounded-xl bg-[#0B6BD3] text-white text-sm font-bold hover:bg-[#0950A0] transition">Join now&rarr;</a>
            @endguest
        </div>
    </div>
</x-info-layout>
