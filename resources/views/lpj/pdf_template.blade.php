@php
    /*
     | Desain PDF LPJ
     | - Tipografi: seluruhnya serif (Times) agar formal; angka rata kanan.
     |   Hierarki ukuran: judul 20 → label 8 (kapital, renggang) → isi 10.5 → total 10.5 tebal → saldo 14.
     | - Warna: navy sebagai identitas (header tabel, baris saldo, aksen), hijau = debit (masuk),
     |   merah = kredit (keluar). Selebihnya putih & abu-abu netral.
     | - Sudut membulat (6–10px) agar terkesan modern dan tidak kaku.
     */
    $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $printedAt = now()->day.' '.$months[now()->month].' '.now()->year;

    $number = fn ($value) => number_format(abs((float) $value), 0, ',', '.');
    $money = fn ($value) => ((float) $value < 0 ? '-' : '').'Rp '.number_format(abs((float) $value), 0, ',', '.');
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
        body { font-family: 'Times New Roman', Times, serif; font-size: 10.5px; color: #334155; line-height: 1.45; }
        .muted { color: #94a3b8; }
        .right { text-align: right; }
        .center { text-align: center; }
        .in { color: #15803d; }   /* debit: hijau */
        .out { color: #dc2626; }  /* kredit: merah */

        /* ---------- Kop ---------- */
        .eyebrow { font-size: 8px; letter-spacing: 2px; text-transform: uppercase; color: #64748b; text-align: center; }
        .title { font-size: 20px; font-weight: bold; color: #0f2a4a; text-align: center; margin: 6px 0 4px; }
        .accent-rule { width: 48px; height: 3px; background: #0f2a4a; border-radius: 2px; margin: 8px auto 14px; }

        .meta { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .meta td { width: 50%; padding: 7px 10px; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        .meta-label { display: block; font-size: 7.5px; letter-spacing: 1px; text-transform: uppercase; color: #94a3b8; margin-bottom: 2px; }
        .meta-value { font-size: 10.5px; font-weight: bold; color: #1e293b; }

        /* ---------- Tabel (sudut membulat: border-collapse separate) ---------- */
        .ledger { width: 100%; border-collapse: separate; border-spacing: 0; table-layout: fixed; border: 1px solid #0f2a4a; border-radius: 10px; }
        .ledger th {
            background: #0f2a4a; color: #e2e8f0; font-size: 8px; font-weight: bold; letter-spacing: 1px;
            text-transform: uppercase; padding: 9px 8px; text-align: left;
        }
        .ledger thead th:first-child { border-top-left-radius: 9px; }
        .ledger thead th:last-child { border-top-right-radius: 9px; }
        .ledger th.right, .ledger td.right { text-align: right; }
        .ledger th.center, .ledger td.center { text-align: center; }
        .ledger td { padding: 7px 8px; border-bottom: 1px solid #e2e8f0; vertical-align: top; word-wrap: break-word; }
        .ledger tbody tr.even td { background: #f8fafc; }
        .ledger .num { text-align: right; white-space: nowrap; }
        .ledger .no { text-align: center; color: #94a3b8; }
        .empty { color: #cbd5e1; }
        .note-yes { color: #475569; font-size: 9.5px; }
        .note-no { color: #94a3b8; font-size: 9.5px; }

        .ledger tr.total td { background: #f1f5f9; border-bottom: none; font-weight: bold; color: #1e293b; }
        .ledger tr.total td.in { color: #15803d; }
        .ledger tr.total td.out { color: #dc2626; }
        .ledger tr.balance > td { background: #0f2a4a; color: #fff; padding: 10px 8px; border-bottom: none; border-bottom-left-radius: 9px; border-bottom-right-radius: 9px; }
        .balance-inner { width: 100%; border-collapse: collapse; }
        .balance-inner td { padding: 0; border: none; vertical-align: middle; }
        .balance-label { font-size: 9px; letter-spacing: 1.5px; text-transform: uppercase; font-weight: bold; color: #cbd5e1; }
        .balance-value { font-size: 14px; font-weight: bold; color: #4ade80; }          /* hijau terang: kontras di atas navy */
        .negative .balance-value { color: #fca5a5; }                                     /* merah terang: kontras di atas navy */
        .balance-hint { display: block; font-size: 8.5px; font-weight: normal; color: #fca5a5; letter-spacing: 0; text-transform: none; margin-top: 2px; }

        /* ---------- Tanda tangan ---------- */
        .signature { width: 100%; margin-top: 34px; }
        .signature td { vertical-align: top; }
        .sign-box { text-align: center; }
        .sign-place { font-size: 10.5px; color: #475569; }
        .sign-image-box { height: 64px; margin: 4px 0; }
        .sign-image { max-height: 64px; max-width: 170px; }
        .sign-empty { height: 64px; }
        .sign-name { display: inline-block; min-width: 150px; padding-top: 3px; border-top: 1px solid #334155; font-weight: bold; color: #1e293b; font-size: 11px; }
        .sign-role { font-size: 9px; color: #94a3b8; }

        /* ---------- Lampiran nota ---------- */
        .page-break { page-break-after: always; }
        .section-title { font-size: 14px; font-weight: bold; color: #0f2a4a; margin: 0 0 12px; padding-bottom: 6px; border-bottom: 2px solid #e2e8f0; }
        .gallery { width: 100%; border-collapse: separate; border-spacing: 8px; margin: 0 -8px; }
        .gallery > tr > td, .gallery td.cell { width: 50%; vertical-align: top; }
        .nota { border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px; text-align: center; }
        .nota-head { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .nota-head td { vertical-align: middle; padding: 0; text-align: left; }
        .nota-no { width: 22px; height: 18px; background: #0f2a4a; color: #fff; border-radius: 5px; font-size: 9px; font-weight: bold; text-align: center !important; }
        .nota-desc { padding-left: 7px !important; font-size: 10.5px; font-weight: bold; color: #1e293b; }
        .nota-image { max-width: 100%; max-height: 240px; border-radius: 6px; }
        .nota-missing { border: 1px dashed #cbd5e1; border-radius: 6px; padding: 30px 10px; color: #94a3b8; font-size: 8.5px; }
        .nota-foot { margin-top: 8px; padding-top: 6px; border-top: 1px solid #f1f5f9; font-size: 10px; text-align: right; }
    </style>
</head>
<body>
    {{-- Kop --}}
    <div class="eyebrow">Lembar Pertanggungjawaban Anggaran</div>
    <div class="title">{{ $report->title }}</div>
    <div class="accent-rule"></div>

    <table class="meta">
        <tr>
            <td><span class="meta-label">Penanggung jawab</span><span class="meta-value">{{ $report->creator_name }}</span></td>
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
                            <span class="note-yes">Terlampir{{ $entry->images->count() > 1 ? ' ('.$entry->images->count().')' : '' }}</span>
                        @else
                            <span class="note-no">Tidak terlampir</span>
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
                <td class="num out">{{ $number($report->total_credit) }}</td>
            </tr>
            {{-- Satu sel penuh (tanpa sekat) agar sudut bawah membulat rapi --}}
            <tr class="balance {{ $isNegative ? 'negative' : '' }}">
                <td colspan="5">
                    <table class="balance-inner">
                        <tr>
                            <td style="width: 62%;" class="right">
                                <span class="balance-label">Saldo Akhir</span>
                                @if ($isNegative)
                                    <span class="balance-hint">Pengeluaran melebihi dana masuk</span>
                                @endif
                            </td>
                            <td style="width: 38%;" class="right"><span class="balance-value">{{ $money($report->balance) }}</span></td>
                        </tr>
                    </table>
                </td>
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
        <div class="section-title">Lampiran Bukti Transaksi</div>

        <table class="gallery">
            @foreach ($receipts->chunk(2) as $row)
                <tr>
                    @foreach ($row as ['no' => $no, 'entry' => $entry, 'image' => $image])
                        @php
                            $dataUri = $image->toDataUri();
                        @endphp
                        <td class="cell">
                            <div class="nota">
                                <table class="nota-head"><tr><td class="nota-no">{{ $no }}</td><td class="nota-desc">{{ $entry->description }}</td></tr></table>

                                @if ($dataUri)
                                    <img src="{{ $dataUri }}" class="nota-image">
                                @else
                                    <div class="nota-missing">File gambar tidak ditemukan<br><span style="font-size: 6px;">{{ $image->image_path }}</span></div>
                                @endif

                                <div class="nota-foot {{ $entry->isDebit() ? 'in' : 'out' }}">{{ $entry->isDebit() ? 'Debit' : 'Kredit' }} · Rp {{ $number($entry->amount) }}</div>
                            </div>
                        </td>
                    @endforeach
                    @if ($row->count() < 2) <td class="cell"></td> @endif
                </tr>
            @endforeach
        </table>
    @endif
</body>
</html>
