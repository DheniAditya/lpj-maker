@php
    /*
     | Desain PDF LPJ — sama dengan LPJ Maker versi publik.
     | - Monokrom: hitam (#111) untuk isi, abu-abu (#666) untuk label, garis tipis (#ccc).
     | - IBM Plex Sans untuk teks, IBM Plex Mono untuk angka (lisensi OFL, file di public/fonts;
     |   didaftarkan di App\Support\LpjPdf sebelum render).
     | - Saldo berjalan per baris; angka minus ditulis dengan tanda minus, contoh -4.000.
     | - Lampiran: 4 bukti per halaman (2×2), nomor sama dengan nomor transaksi.
     */
    use App\Support\Terbilang;

    $months = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $printedAt = now()->day.' '.$months[now()->month].' '.now()->year;

    $num = fn ($v) => number_format(abs((float) $v), 0, ',', '.');
    /** Angka bertanda: minus ditulis dengan tanda minus, contoh -450.000 / -Rp450.000. */
    $signed = fn ($v, $rp = false) => ((float) $v < 0 ? '-' : '').($rp ? 'Rp' : '').$num($v);


    $saldo = 0;
    $rows = $report->entries->values()->map(function ($entry, $i) use (&$saldo) {
        $saldo += $entry->isDebit() ? $entry->amount : -$entry->amount;

        return ['no' => $i + 1, 'entry' => $entry, 'saldo' => $saldo];
    });

    $receipts = $report->entries->values()->flatMap(
        fn ($entry, $index) => $entry->images->values()->map(fn ($image, $k) => [
            'no' => $index + 1, 'entry' => $entry, 'image' => $image, 'k' => $k + 1, 'n' => $entry->images->count(),
        ])
    );
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LPJ - {{ $report->title }}</title>
    <style>

        @page { size: A4 portrait; margin: 16mm 17mm 22mm 17mm; }
        body { font-family: 'Plex', sans-serif; font-size: 9pt; color: #111; line-height: 1.25; margin: 0; }
        table { border-collapse: collapse; }
        .mono { font-family: 'PlexMono', monospace; }
        .gray { color: #666; }
        .right { text-align: right; }
        .center { text-align: center; }

        /* ---------- Kop ---------- */
        .kicker { font-size: 7.5pt; letter-spacing: 2.6pt; color: #666; }
        .title { font-size: 17pt; font-weight: bold; margin-top: 2mm; line-height: 1.2; }
        .rule-thick { border-top: 0.5mm solid #111; margin: 4mm 0 5mm; }

        .meta { width: 100%; margin-bottom: 4mm; }
        .meta td { padding: 0; font-size: 9pt; }
        .meta .label { color: #666; }
        .meta .value { font-weight: bold; font-size: 9.5pt; padding-top: 1.6mm; }

        /* ---------- Tabel ---------- */
        .ledger { width: 100%; table-layout: fixed; }
        .ledger th { font-weight: bold; text-align: left; padding: 2.4mm 2mm; border-top: 0.3mm solid #111; border-bottom: 0.3mm solid #111; }
        .ledger td { padding: 2.4mm 2mm; vertical-align: top; border-bottom: 0.2mm solid #ccc; }
        .ledger tr.last td { border-bottom: 0.3mm solid #111; }
        .ledger .right, .ledger td.num { text-align: right; }
        .ledger td.num { font-family: 'PlexMono', monospace; white-space: nowrap; }
        .ledger td.no, .ledger th.no { text-align: center; }

        /* ---------- Ringkasan ---------- */
        .summary { width: 45%; margin: 6mm 0 0 55%; }
        .summary td { padding: 1mm 0; }
        .summary .val { text-align: right; font-family: 'PlexMono', monospace; }
        .summary tr.sep td { border-bottom: 0.5mm solid #111; padding-bottom: 1.8mm; }
        .summary tr.total td { font-weight: bold; padding-top: 2.4mm; }

        .terbilang { background: #f2f2f2; padding: 3mm 4mm; margin-top: 5mm; font-size: 8.5pt; }
        .terbilang b { font-weight: bold; }
        .terbilang i { font-style: italic; }

        /* ---------- Tanda tangan ---------- */
        .sign { margin: 12mm 0 0 auto; width: 52mm; page-break-inside: avoid; }
        .sign td { padding: 0; }
        .sign .hormat { font-size: 9pt; padding-bottom: 1mm; }
        .sign .img { height: 21mm; vertical-align: bottom; border-bottom: 0.25mm solid #111; }
        .sign .img img { max-height: 20mm; max-width: 42mm; }
        .sign .name { font-weight: bold; font-size: 9.5pt; padding-top: 1.6mm; }
        .sign .role { color: #666; font-size: 9pt; }

        /* ---------- Lampiran ---------- */
        .page-break { page-break-before: always; }
        .att-kicker { font-size: 7.5pt; letter-spacing: 2.6pt; color: #666; }
        .att-title { font-size: 13pt; font-weight: bold; margin-top: 1.5mm; }
        .att-rule { border-top: 0.5mm solid #111; margin: 2mm 0 4mm; }
        .grid { width: 100%; table-layout: fixed; }
        .grid td.cell { width: 48.3%; vertical-align: top; padding: 0; }
        .grid td.gap { width: 3.4%; }
        .grid tr.gap-row td { height: 6mm; }
        .card { border: 0.25mm solid #ccc; height: 112mm; }
        .card-head { width: 100%; height: 11mm; }
        .card-head td { vertical-align: top; padding: 3.5mm 0 0; font-size: 8.5pt; }
        .card-head td.nota-no { width: 10mm; padding-left: 4mm; font-family: 'PlexMono', monospace; font-weight: bold; font-size: 9pt; }
        .card-head td.nota-desc { padding-right: 4mm; }
        .card-img { width: 100%; }
        .card-img td { height: 88mm; text-align: center; vertical-align: middle; padding: 0 4mm; }
        .card-img img { max-width: 76mm; max-height: 86mm; }
        .card-foot { margin: 0 4mm; border-top: 0.25mm solid #ccc; text-align: right; font-family: 'PlexMono', monospace; padding-top: 2mm; }
        .missing { color: #999; font-size: 8pt; }
    </style>
</head>
<body>
    {{-- Kop --}}
    <div class="kicker">LAPORAN PERTANGGUNGJAWABAN</div>
    <div class="title">{{ $report->title }}</div>
    <div class="rule-thick"></div>

    <table class="meta">
        <tr>
            <td class="label">Penanggungjawab</td>
            <td class="label right">Tanggal Laporan</td>
        </tr>
        <tr>
            <td class="value">{{ $report->creator_name }}</td>
            <td class="value right">{{ $printedAt }}</td>
        </tr>
    </table>

    {{-- Rincian transaksi --}}
    <table class="ledger">
        <thead>
            <tr>
                <th class="no" style="width: 7.4%;">No</th>
                <th style="width: 35.2%;">Keterangan</th>
                <th style="width: 16.5%;">Bukti</th>
                <th class="right" style="width: 13.6%;">Debit</th>
                <th class="right" style="width: 13.6%;">Kredit</th>
                <th class="right" style="width: 13.7%;">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as ['no' => $no, 'entry' => $entry, 'saldo' => $rowSaldo])
                @php $count = $entry->images->count(); @endphp
                <tr class="{{ $loop->last ? 'last' : '' }}">
                    <td class="no">{{ $no }}</td>
                    <td>{{ $entry->description }}</td>
                    <td>{{ $count ? 'Terlampir'.($count > 1 ? " ({$count})" : '') : ($entry->isDebit() ? '-' : 'Tidak terlampir') }}</td>
                    <td class="num">{{ $entry->isDebit() ? $num($entry->amount) : '-' }}</td>
                    <td class="num">{{ $entry->isDebit() ? '-' : $num($entry->amount) }}</td>
                    <td class="num">{{ $signed($rowSaldo) }}</td>
                </tr>
            @empty
                <tr class="last"><td colspan="6" class="center gray">Belum ada transaksi.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Ringkasan --}}
    <table class="summary">
        <tr><td class="gray">Total Debit (dana masuk)</td><td class="val">Rp{{ $num($report->total_debit) }}</td></tr>
        <tr class="sep"><td class="gray">Total Kredit (pengeluaran)</td><td class="val">Rp{{ $num($report->total_credit) }}</td></tr>
        <tr class="total"><td>Saldo Akhir</td><td class="val">{{ $signed($report->balance, true) }}</td></tr>
    </table>

    <div class="terbilang"><b>Terbilang:</b> <i>{{ Terbilang::rupiah($report->balance) }}</i></div>

    {{-- Tanda tangan --}}
    <table class="sign">
        <tr><td class="hormat">Hormat Kami,</td></tr>
        <tr>
            <td class="img">
                @if ($signature = $report->signatureDataUri())
                    <img src="{{ $signature }}">
                @endif
            </td>
        </tr>
        <tr><td class="name">{{ $report->creator_name }}</td></tr>
        <tr><td class="role">Penanggung Jawab</td></tr>
    </table>

    {{-- Lampiran bukti: 4 per halaman --}}
    @foreach ($receipts->chunk(4) as $page)
        <div class="page-break"></div>
        <div class="att-kicker">LAMPIRAN BUKTI TRANSAKSI</div>
        <div class="att-title">{{ $report->title }}</div>
        <div class="att-rule"></div>

        <table class="grid">
            @foreach ($page->values()->chunk(2) as $pair)
                @if (! $loop->first)
                    <tr class="gap-row"><td colspan="3"></td></tr>
                @endif
                <tr>
                    @foreach ($pair->values() as $i => ['no' => $no, 'entry' => $entry, 'image' => $image, 'k' => $k, 'n' => $n])
                        @if ($i === 1)
                            <td class="gap"></td>
                        @endif
                        <td class="cell">
                            <div class="card">
                                <table class="card-head">
                                    <tr><td class="nota-no">{{ str_pad($no, 2, '0', STR_PAD_LEFT) }}</td><td class="nota-desc">{{ $entry->description }}{{ $n > 1 ? " ({$k}/{$n})" : '' }}</td></tr>
                                </table>
                                <table class="card-img">
                                    <tr>
                                        <td>
                                            @if ($dataUri = $image->toDataUri())
                                                <img src="{{ $dataUri }}">
                                            @else
                                                <span class="missing">File gambar tidak ditemukan</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                                <div class="card-foot">{{ $entry->isDebit() ? '+' : '-' }}{{ $num($entry->amount) }}</div>
                            </div>
                        </td>
                    @endforeach
                    @if ($pair->count() < 2)
                        <td class="gap"></td><td class="cell"></td>
                    @endif
                </tr>
            @endforeach
        </table>
    @endforeach
</body>
</html>
