<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LPJ - {{ $report->title }}</title>
    <style>
        @page { margin: 2.5cm 1.5cm; size: A4 portrait; }
        
        body { 
            font-family: 'Times New Roman', Times, Georgia, serif; 
            font-size: 10px; 
            color: #334155; 
            line-height: 1.5; 
            background-color: #fff; 
        }
        .container { width: 100%; margin: 0 auto; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .uppercase { text-transform: uppercase; }

        /* Header Section */
        .header-section { text-align: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px; }
        .report-label { font-size: 9px; font-weight: 600; color: #64748b; display: block; margin-bottom: 5px; }
        .report-title { font-size: 20px; font-weight: 800; color: #1e293b; margin: 0; }

        /* 
           Table Style - Diperbarui untuk mendukung border-radius 10px di DomPDF
        */
        table.main-table { 
            width: 100%; 
            border-collapse: separate; /* Wajib diubah ke separate agar border-radius berfungsi */
            border-spacing: 0; 
            margin-top: 10px; 
            table-layout: fixed; 
            border: 1px solid #cbd5e1; /* Outline luar tabel */
            border-radius: 10px; /* Lengkungan sudut tabel */
            overflow: hidden; /* Memastikan background tidak keluar dari lengkungan */
        }

        /* 1. Pojok Kiri Atas (Header Pertama) */
        .main-table thead tr:first-child th:first-child {
            border-top-left-radius: 9px; /* Sedikit lebih kecil dari 10px agar pas di dalam border luar */
        }

        /* 2. Pojok Kanan Atas (Header Terakhir) */
        .main-table thead tr:first-child th:last-child {
            border-top-right-radius: 9px;
        }

        /* 3. Pojok Kiri Bawah (Saldo Akhir Kiri) */
        .main-table tfoot tr.balance-row td:first-child {
            border-bottom-left-radius: 9px;
        }

        /* 4. Pojok Kanan Bawah (Saldo Akhir Kanan) */
        .main-table tfoot tr.balance-row td:last-child {
            border-bottom-right-radius: 9px;
        }

        /* Styling Elemen Header */
        .main-table thead th { 
            background-color: #1e293b; 
            color: #e2e8f0; 
            padding: 10px 5px; 
            text-align: center; 
            font-size: 14px; 
            border-bottom: 1px solid #cbd5e1;
            border-right: 1px solid #334155; /* Sekat antar header */
        }

        /* Styling Baris Data */
        .main-table tbody td { 
            padding: 10px 5px; 
            border-bottom: 1px solid #e2e8f0; 
            border-right: 1px solid #e2e8f0; /* Sekat vertikal */
            vertical-align: middle; 
            word-wrap: break-word; 
        }

        /* Menghilangkan sisa border kanan pada kolom terakhir untuk menghindari garis ganda */
        .main-table thead th:last-child,
        .main-table tbody td:last-child,
        .main-table tfoot td:last-child {
            border-right: none;
        }
        
        /* Padding khusus angka agar tidak menempel border kanan */
        .pr-10 { padding-right: 10px !important; }

        /* Row Total Mutasi */
        .total-row td { 
            background-color: #f8fafc; 
            border-bottom: 1px solid #cbd5e1; 
            border-right: 1px solid #cbd5e1; 
            padding: 10px 5px; 
        }

        /* Row Saldo Akhir */
        .balance-row td { 
            background-color: #1e293b; 
            color: #ffffff; 
            padding: 12px 10px; 
            border-bottom: none; /* Bagian paling bawah mengandalkan border luar table */
        }
        
        .credit-text { color: #dc2626 !important; }
        .debit-text { color: #16a34a !important; }

        /* Signature */
        .signature-section { margin-top: 30px; }
        .signature-line { border-bottom: 1px solid #334155; display: inline-block; font-weight: bold; min-width: 150px; padding-top: 50px; }

        /* Gallery/Nota */
        .page-break { page-break-after: always; }
        .gallery-header { font-size: 14px; font-weight: bold; border-bottom: 2px solid #e2e8f0; padding-bottom: 5px; margin-bottom: 15px; }
        .gallery-table { width: 100%; border-collapse: collapse; }
        .gallery-td { width: 50%; padding: 10px; vertical-align: top; }
        .nota-box { border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; text-align: center; background-color: #f8fafc; }
        .nota-image { max-width: 100%; max-height: 250px; border-radius: 4px; margin: 10px 0; }
        .nota-title { display: block; color: #1e293b; font-size: 11px; margin-bottom: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-section">
            <span class="report-label uppercase">Lembar Pertanggungjawaban Anggaran</span>
            <h1 class="report-title">{{ $report->title }}</h1>
            <div style="margin-top: 5px; font-size: 9px; font-weight: 400; color: #64748b;">Dicetak pada {{ date('d F Y') }}</div>
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 40%;">Keterangan</th>
                    <th style="width: 16%;">Nota</th>
                    <th style="width: 18%;">Debit</th>
                    <th style="width: 18%;">Kredit</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($report->entries as $entry)
                <tr>
                    <td class="text-center">{{ $entry->created_at->format('d/m/Y') }}</td>
                    <td>{{ $entry->description }}</td>
                    <td class="text-center">
                        <span style="color: #64748b; font-size: 10px;">
                            {{ $entry->images->isNotEmpty() ? 'Terlampir ('.$entry->images->count().')' : 'Tidak Ada' }}
                        </span>
                    </td>
                    <td class="text-right pr-10 {{ $entry->isDebit() ? 'debit-text' : '' }}">
                        @if ($entry->isDebit()) @rupiah($entry->amount) @else - @endif
                    </td>
                    <td class="text-right pr-10 {{ $entry->isDebit() ? '' : 'credit-text' }}">
                        @if ($entry->isDebit()) - @else @rupiah($entry->amount) @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">Tidak ada data entri.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" class="text-center pr-10">TOTAL MUTASI</td>
                    <td class="text-center pr-10 debit-text">@rupiah($report->total_debit)</td>
                    <td class="text-center pr-10 credit-text">@rupiah($report->total_credit)</td>
                </tr>
                <tr class="balance-row">
                    <td colspan="3" class="text-center pr-10" style="font-weight: bold; text-transform: uppercase; font-family:'Times New Roman', Times, serif; font-size: 14px;">Saldo Akhir</td>
                    <td colspan="2" class="text-center pr-10 {{ $report->balance < 0 ? 'credit-text' : 'debit-text' }}" style="font-weight: 800; font-size: 14px;">
                        @rupiah($report->balance)
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="signature-section">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 65%;"></td>
                    <td style="width: 35%; text-align: center;">
                        <span>Hormat Kami,</span><br>
                        <div class="signature-line">{{ $report->creator_name }}</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- HALAMAN LAMPIRAN NOTA (2 kolom per baris) --}}
        @php
            $receipts = $report->entries->flatMap(
                fn ($entry) => $entry->images->map(fn ($image) => ['entry' => $entry, 'image' => $image])
            );
        @endphp

        @if ($receipts->isNotEmpty())
            <div class="page-break"></div>
            <h2 class="gallery-header">Lampiran Bukti Transaksi</h2>

            <table class="gallery-table">
                @foreach ($receipts->chunk(2) as $row)
                    <tr>
                        @foreach ($row as ['entry' => $entry, 'image' => $image])
                            @php($dataUri = $image->toDataUri())
                            <td class="gallery-td">
                                <div class="nota-box">
                                    <span class="nota-title">{{ $entry->description }}</span>

                                    @if ($dataUri)
                                        <img src="{{ $dataUri }}" class="nota-image">
                                    @else
                                        <div style="border: 1px dashed #cbd5e1; padding: 30px 10px; color: #94a3b8; font-size: 10px;">
                                            Gambar tidak ditemukan fisik<br>
                                            <span style="font-size: 6px;">{{ $image->image_path }}</span>
                                        </div>
                                    @endif

                                    <div style="font-size: 10px; color: #475569; margin-top: 5px; border-top: 1px solid #e2e8f0; padding-top: 5px;">
                                        @rupiah($entry->amount)
                                    </div>
                                </div>
                            </td>
                        @endforeach
                        @if ($row->count() < 2) <td class="gallery-td"></td> @endif
                    </tr>
                @endforeach
            </table>
        @endif
    </div>
</body>
</html>