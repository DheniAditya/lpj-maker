@php
    $faqs = [
        ['Saya lupa password, bagaimana?', 'Buka halaman Lupa password?, isi username, lalu Kirim permintaan ke admin. Admin akan membuatkan password baru dan memberitahukannya kepadamu. Bisa juga Chat admin di WhatsApp.'],
        ['Kenapa tombol Download PDF terkunci?', 'PDF baru bisa diunduh setelah LPJ ditandatangani dan semua transaksi Kredit memiliki nota. Tekan Tanda Tangan, pilih Tulis atau Gambar, lalu Simpan Tanda Tangan. Transaksi Debit boleh tanpa nota.'],
        ['Berapa banyak LPJ yang bisa disimpan?', 'Setiap akun bisa menyimpan sampai '.\App\Models\User::MAX_REPORTS.' arsip laporan. Jika penuh, hapus LPJ lama di Dashboard untuk membuat yang baru.'],
        ['Apa bedanya Pelihat dan Editor saat Bagikan?', 'Pelihat hanya melihat & unduh PDF. Editor bisa tambah/ubah transaksi. Link bisa dibatalkan kapan saja lewat Batalkan link share.'],
        ['Kamera tidak bisa dibuka di HP?', 'Pastikan browser diizinkan memakai kamera. Kamera belakang terbuka lebih dulu; tekan tombol putar untuk ganti ke kamera depan.'],
        ['Apakah LPJ di Mode Tamu tersimpan?', 'Anda berada di mode tamu. Riwayat pekerjaan anda akan terhapus otomatis setelah halaman ini di tutup. Silahkan download LPJ anda sebelum menutup halaman.'],
    ];
    $wa = 'https://wa.me/'.config('lpj.admin_whatsapp');
@endphp
<x-info-layout title="Bantuan" kicker="Bantuan" heading="Ada kendala?"
               sub="Cek pertanyaan yang sering muncul, atau langsung hubungi admin.">

    <div class="grid gap-8 lg:grid-cols-[2fr_1fr] items-start">
        <section x-data="{ open: 0 }" aria-label="Pertanyaan yang sering muncul" class="flex flex-col gap-3">
            @foreach ($faqs as $i => [$q, $a])
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
                    <button type="button" @click="open = open === {{ $i }} ? -1 : {{ $i }}" :aria-expanded="open === {{ $i }}"
                            class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left font-bold text-[#0f2a4a] hover:bg-slate-50">
                        <span>{{ $q }}</span>
                        <x-lpj.icon name="chevron-down" class="w-5 h-5 shrink-0 text-slate-500 transition-transform" ::class="open === {{ $i }} && 'rotate-180'" />
                    </button>
                    <div x-show="open === {{ $i }}" @if ($i !== 0) x-cloak @endif class="px-5 pb-5 -mt-1 text-sm leading-relaxed text-slate-600">{{ $a }}</div>
                </div>
            @endforeach
        </section>

        <aside class="bg-white border border-slate-200 rounded-3xl p-7">
            <h2 class="text-xl font-extrabold text-[#0f2a4a]">Hubungi admin</h2>
            <p class="mt-2.5 text-sm leading-relaxed text-slate-600">Lupa password, akun bermasalah, atau butuh bantuan lain? Chat admin LPJ Maker.</p>
            <a href="{{ $wa }}" target="_blank" rel="noopener" class="mt-5 w-full min-h-[50px] inline-flex items-center justify-center gap-2 rounded-xl bg-[#25D366] hover:bg-[#1ebe5b] text-white font-bold transition">
                @include('auth.partials.whatsapp-logo')
                Chat admin di WhatsApp
            </a>
            <a href="{{ route('password.request') }}" class="mt-2.5 w-full min-h-[46px] inline-flex items-center justify-center rounded-xl border border-slate-200 text-[#0f172a] text-sm font-bold hover:bg-slate-50 transition">Lupa password?</a>
        </aside>
    </div>
</x-info-layout>
