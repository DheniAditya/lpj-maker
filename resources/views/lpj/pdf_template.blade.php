@php
    /*
     | Desain PDF LPJ
     | - Tipografi: judul serif (formal), isi & angka sans-serif DejaVu (angka lebar sama → rapi rata kanan).
     |   Hierarki ukuran: judul 18 → label 7.5 (kapital, renggang) → isi 9.5 → total 10 → saldo 13.
     | - Warna 60-30-10: putih (dominan) · abu-abu slate untuk teks, garis, latar header · navy sebagai aksen
     |   (garis judul, baris saldo). Hijau redup hanya untuk dana masuk; merah hanya jika saldo minus
     |   (sinyal peringatan), bukan untuk setiap pengeluaran.
     */
    $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $printedAt = now()->day.' '.$months[now()->month].' '.now()->year;

    $number = fn ($value) => number_format(abs((float) $value), 0, ',', '.');
    $money = fn ($value) => ((float) $value < 0 ? '−' : '').'Rp '.number_format(abs((float) $value), 0, ',', '.');
    $isNegative = $report->balance < 0;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LPJ - {{ $report->title }}</title>
    <style>
        @page { margin: 2.2cm 1.8cm; size: A4 portrait; }

        /* ---------- Dasar ---------- */
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5px; color: #334155; line-height: 1.45; }
        .serif { font-family: 'DejaVu Serif', 'Times New Roman', serif; }
        .muted { color: #94a3b8; }
        .right { text-align: right; }
        .center { text-align: center; }

        /* ---------- Kop ---------- */
        .eyebrow { font-size: 7.5px; letter-spacing: 2px; text-transform: uppercase; color: #64748b; text-align: center; }
        .title { font-size: 18px; font-weight: bold; color: #0f2a4a; text-align: center; margin: 6px 0 4px; }
        .accent-rule { width: 48px; height: 2px; background: #0f2a4a; margin: 8px auto 14px; }

        .meta { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .meta td { width: 33.33%; padding: 7px 10px; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .meta-label { display: block; font-size: 7px; letter-spacing: 1px; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px; }
        .meta-value { font-size: 9.5px; font-weight: bold; color: #1e293b; }

        /* ---------- Tabel ---------- */
        .ledger { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .ledger th {
            background: #f1f5f9; color: #475569; font-size: 7.5px; font-weight: bold; letter-spacing: 1px;
            text-transform: uppercase; padding: 8px 8px; border-bottom: 1.5px solid #0f2a4a; text-align: left;
        }
        .ledger th.right, .ledger td.right { text-align: right; }
        .ledger th.center, .ledger td.center { text-align: center; }
        .ledger td { padding: 7px 8px; border-bottom: 1px solid #e2e8f0; vertical-align: top; word-wrap: break-word; }
        .ledger tbody tr.even td { background: #fafbfc; }
        .ledger .num { text-align: right; white-space: nowrap; }
        .ledger .no { text-align: center; color: #94a3b8; }
        .in { color: #15803d; }                 /* dana masuk: hijau redup */
        .out { color: #1e293b; }                /* pengeluaran: netral, bukan merah */
        .empty { color: #cbd5e1; }
        .note-yes { color: #475569; font-size: 8.5px; }
        .note-no { color: #b6c0cd; font-size: 8.5px; }

        .ledger tr.total td { background: #f8fafc; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1; font-weight: bold; color: #1e293b; font-size: 9.5px; }
        .ledger tr.balance td { background: #eef2f7; border-bottom: 2px solid #0f2a4a; padding: 10px 8px; }
        .ledger tr.balance.negative td { background: #fef2f2; border-bottom-color: #b91c1c; }
        .balance-label { font-size: 8px; letter-spacing: 1.5px; text-transform: uppercase; font-weight: bold; color: #0f2a4a; }
        .balance-value { font-size: 13px; font-weight: bold; color: #0f2a4a; }
        .negative .balance-label, .negative .balance-value { color: #b91c1c; }
        .balance-hint { display: block; font-size: 7.5px; font-weight: normal; color: #b91c1c; letter-spacing: 0; text-transform: none; margin-top: 2px; }

        /* ---------- Tanda tangan ---------- */
        .signature { width: 100%; margin-top: 34px; }
        .signature td { vertical-align: top; }
        .sign-box { text-align: center; }
        .sign-place { font-size: 9.5px; color: #475569; }
        .sign-image-box { height: 64px; margin: 4px 0; }
        .sign-image { max-height: 64px; max-width: 170px; }
        .sign-empty { height: 64px; }
        .sign-name { display: inline-block; min-width: 150px; padding-top: 3px; border-top: 1px solid #334155; font-weight: bold; color: #1e293b; font-size: 10px; }
        .sign-role { font-size: 8px; color: #94a3b8; }

        /* ---------- Lampiran nota ---------- */
        .page-break { page-break-after: always; }
        .section-title { font-size: 12px; font-weight: bold; color: #0f2a4a; margin: 0 0 4px; }
        .section-sub { font-size: 8.5px; color: #94a3b8; margin-bottom: 12px; }
        .gallery { width: 100%; border-collapse: separate; border-spacing: 8px; margin: 0 -8px; }
        .gallery td { width: 50%; vertical-align: top; }
        .nota { border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px; text-align: center; }
        .nota-head { text-align: left; font-size: 9px; color: #1e293b; font-weight: bold; margin-bottom: 6px; }
        .nota-no { display: inline-block; min-width: 14px; padding: 1px 4px; margin-right: 4px; background: #0f2a4a; color: #fff; border-radius: 3px; font-size: 8px; text-align: center; }
        .nota-image { max-width: 100%; max-height: 240px; }
        .nota-missing { border: 1px dashed #cbd5e1; padding: 30px 10px; color: #94a3b8; font-size: 8.5px; }
        .nota-foot { margin-top: 6px; padding-top: 5px; border-top: 1px solid #f1f5f9; font-size: 9px; text-align: right; }
    </style>
</head>
<body>
    {{-- Kop --}}
    <div class="eyebrow">Lembar Pertanggungjawaban Anggaran</div>
    <div class="title serif">{{ $report->title }}</div>
    <div class="accent-rule"></div>

    <table class="meta">
        <tr>
            <td><span class="meta-label">Penanggung jawab</span><span class="meta-value">{{ $report->creator_name }}</span></td>
            <td><span class="meta-label">Jumlah transaksi</span><span class="meta-value">{{ $report->entries->count() }} transaksi</span></td>
            <td class="right"><span class="meta-label">Dicetak pada</span><span class="meta-value">{{ $printedAt }}</span></td>
        </tr>
    </table>

    {{-- Rincian --}}
    <table class="ledger">
        <thead>
            <tr>
                <th style="width: 6%;" class="center">No</th>
                <th style="width: 42%;">Keterangan</th>
                <th style="width: 14%;" class="center">Nota</th>
                <th style="width: 19%;" class="right">Debit (Rp)</th>
                <th style="width: 19%;" class="right">Kredit (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($report->entries as $entry)
                <tr class="{{ $loop->even ? 'even' : '' }}">
                    <td class="no">{{ $loop->iteration }}</td>
                    <td>{{ $entry->description }}</td>
                    <td class="center">
                        @if ($entry->images->isNotEmpty())
                            <span class="note-yes">Lampiran {{ $loop->iteration }}{{ $entry->images->count() > 1 ? ' ('.$entry->images->count().')' : '' }}</span>
                        @else
                            <span class="note-no">—</span>
                        @endif
                    </td>
                    <td class="num {{ $entry->isDebit() ? 'in' : 'empty' }}">{{ $entry->isDebit() ? $number($entry->amount) : '—' }}</td>
                    <td class="num {{ $entry->isDebit() ? 'empty' : 'out' }}">{{ $entry->isDebit() ? '—' : $number($entry->amount) }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="center muted" style="padding: 18px;">Belum ada transaksi.</td></tr>
            @endforelse

            <tr class="total">
                <td colspan="3" class="right">Total</td>
                <td class="num in">{{ $number($report->total_debit) }}</td>
                <td class="num">{{ $number($report->total_credit) }}</td>
            </tr>
            <tr class="balance {{ $isNegative ? 'negative' : '' }}">
                <td colspan="3" class="right">
                    <span class="balance-label">Saldo Akhir</span>
                    @if ($isNegative)
                        <span class="balance-hint">Pengeluaran melebihi dana masuk</span>
                    @endif
                </td>
                <td colspan="2" class="num"><span class="balance-value">{{ $money($report->balance) }}</span></td>
            </tr>
        </tbody>
    </table>

    {{-- Tanda tangan --}}
    <table class="signature">
        <tr>
            <td style="width: 62%;"></td>
            <td style="width: 38%;" class="sign-box">
                <div class="sign-place">Hormat kami,</div>
                @if ($signature = $report->signatureDataUri())
                    <div class="sign-image-box"><img src="{{ $signature }}" class="sign-image"></div>
                @else
                    <div class="sign-empty"></div>
                @endif
                <div class="sign-name">{{ $report->creator_name }}</div>
                <div class="sign-role">Penanggung Jawab</div>
            </td>
        </tr>
    </table>

    {{-- Lampiran nota (2 kolom), nomor sama dengan nomor transaksi --}}
    @php
        $receipts = $report->entries->values()->flatMap(
            fn ($entry, $index) => $entry->images->map(fn ($image) => ['no' => $index + 1, 'entry' => $entry, 'image' => $image])
        );
    @endphp

    @if ($receipts->isNotEmpty())
        <div class="page-break"></div>
        <div class="section-title serif">Lampiran Bukti Transaksi</div>
        <div class="section-sub">Nomor lampiran sesuai nomor transaksi pada tabel rincian.</div>

        <table class="gallery">
            @foreach ($receipts->chunk(2) as $row)
                <tr>
                    @foreach ($row as ['no' => $no, 'entry' => $entry, 'image' => $image])
                        @php
                            $dataUri = $image->toDataUri();
                        @endphp
                        <td>
                            <div class="nota">
                                <div class="nota-head"><span class="nota-no">{{ $no }}</span>{{ $entry->description }}</div>

                                @if ($dataUri)
                                    <img src="{{ $dataUri }}" class="nota-image">
                                @else
                                    <div class="nota-missing">File gambar tidak ditemukan<br><span style="font-size: 6px;">{{ $image->image_path }}</span></div>
                                @endif

                                <div class="nota-foot {{ $entry->isDebit() ? 'in' : 'out' }}">{{ $entry->isDebit() ? 'Debit' : 'Kredit' }} · Rp {{ $number($entry->amount) }}</div>
                            </div>
                        </td>
                    @endforeach
                    @if ($row->count() < 2) <td></td> @endif
                </tr>
            @endforeach
        </table>
    @endif
</body>
</html>
