<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LPJ - {{ $report->title }}</title>
    
    <style>
        @page {
            margin: 2.5cm 2cm;
            size: A4 portrait;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            color: #334155; /* Slate 700 */
            line-height: 1.5;
            background-color: #fff;
        }

        .container { width: 100%; margin: 0 auto; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        /* Warna Aksen Modern */
        .text-primary { color: #3b82f6; } /* Biru Aplikasi */
        .bg-primary-light { background-color: #eff6ff; }

        /* --- HEADER SECTION --- */
        .header-section {
            text-align: center;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 25px;
            margin-bottom: 30px;
        }

        .report-label {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            color: #37465a;; /* Slate 500 */
            display: block;
            margin-bottom: 10px;
        }

        .report-title {
            padding-top:10px;
            font-size: 26px;
            font-weight: 800;
            /* Menggunakan warna aksen biru agar menonjol seperti di aplikasi */
            color: #37465a; 
            margin: 0;
            line-height: 1.2;
        }

        .meta-date {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 10px;
        }

        /* --- MODERN TABLE STYLE --- */
        table.main-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 8px; /* Rounded corners untuk tabel */
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        /* Header Tabel: Dark Slate Background dengan Teks Putih */
        .main-table thead th {
            background-color: #1e293b; /* Slate 900 header */
            color: #ffffff;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            padding: 14px 12px;
            text-align: left;
            letter-spacing: 0.5px;
        }

        /* Body Tabel */
        .main-table tbody td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
            color: #334155;
            background-color: #fff; 
        }

        /* Striped Rows (Opsional, agar lebih mudah dibaca) */
        .main-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* --- FOOTER & TOTALS --- */
        .total-row td {
            background-color: #f1f5f9; /* Slate 100 */
            font-weight: 700;
            border-top: 2px solid #cbd5e1;
            padding: 14px 12px;
            color: #1e293b;
        }

        .balance-row td {
            background-color: #1e293b; /* Dark background untuk penekanan */
            color: #ffffff; /* Teks putih */
            font-weight: 800;
            font-size: 14px;
            padding: 16px 12px;
        }

        /* Highlight Saldo Akhir dengan warna aksen */
        .balance-amount {
            font-size: 18px;
            color: #3b82f6; /* Biru terang */
            background-color: #ffffff;
            padding: 5px 15px;
            padding-bottom: 10px;
            border-radius: 4px;
            display: inline-block;
        }
        
        .debit-text { color: #16a34a; font-weight: 600; } /* Hijau */
        .credit-text { color: #dc2626; font-weight: 600; } /* Merah */
        .neutral-text { color: #94a3b8; }

        /* --- TANDA TANGAN --- */
        .signature-section { margin-top: 50px; }
        .signature-line {
            border-bottom: 1px solid #334155;
            display: inline-block;
            padding-bottom: 5px;
            font-weight: 700;
            color: #1e293b;
            min-width: 150px;
        }

        .page-break { page-break-after: always; }
        
        .gallery-header {
    font-size: 18px;
    font-weight: 800;
    color: #1e293b;
    border-bottom: 3px solid #e2e8f0;
    padding-bottom: 10px;
    margin-bottom: 25px;
    display: inline-block;
}

.gallery-table {
    width: 100%;
    border-collapse: separate; 
    border-spacing: 15px;
    margin-top: -15px;
}

.gallery-td {
    width: 50%;
    vertical-align: top;
}

.nota-card {
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    display: block; 
}

.nota-card-header {
    background-color: #f1f5f9;
    padding: 12px 15px;
    border-bottom: 1px solid #e2e8f0;
}

.nota-title {
    font-size: 13px;
    font-weight: 400;
    color: #1e293b;
    margin: 0;
}

.nota-body {
    padding: 15px;
    text-align: center; 
    height: 240px;      
    display: flex;      
    align-items: center;
    justify-content: center;
    background-color: #f8fafc; 
}

.nota-image {
    height: 100%;        
    width: auto;         
    max-width: 100%;     
    object-fit: contain; 
    display: inline-block; 
    margin: 0 auto;      
    border-radius: 4px;
}
        .nota-footer {
            padding: 10px 15px;
            background-color: #ffffff;
            border-top: 1px dashed #e2e8f0;
        }

        .nota-badge {
            font-size: 11px;
            color: #fdfdfd;
            background-color: #475569; /* Slate badge */
            padding: 6px 12px;
            padding-bottom: 8px;
            border-radius: 20px; /* Pill shape */
            display: inline-block;
            font-weight: 400;
        }
    </style>
</head>
<body>

    <div class="container">
        
        {{-- HEADER SECTION --}}
        <div class="header-section">
            <span class="report-label uppercase">Lembar Pertanggungjawaban Anggaran</span>
            <h1 class="report-title">{{ $report->title }}</h1>
            <div class="meta-date">Dibuat pada: {{ date('d F Y') }}</div>
        </div>

        {{-- TABEL MODERN --}}
        <table class="main-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 15%;">Tanggal</th>
                    <th style="width: 40%;">Keterangan Transaksi</th>
                    <th class="text-right" style="width: 22.5%;">Debit </th>
                    <th class="text-right" style="width: 22.5%;">Kredit </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($report->entries as $entry)
                <tr>
                    <td class="text-center">{{ $entry->created_at->format('d/m/Y') }}</td>
                    <td style="font-weight: 500;">{{ $entry->description }}</td>
                    <td class="text-right">
                        @if($entry->type == 'debit')
                            <span class="debit-text">Rp {{ number_format($entry->amount, 0, ',', '.') }}</span>
                        @else
                            <span class="neutral-text">-</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($entry->type == 'credit')
                            <span class="credit-text">Rp {{ number_format($entry->amount, 0, ',', '.') }}</span>
                        @else
                            <span class="neutral-text">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding: 25px; color: #94a3b8;">
                        Tidak ada data entri transaksi.
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2" class="text-right uppercase">Total Mutasi</td>
                    <td class="text-right debit-text">Rp {{ number_format($report->total_debit, 0, ',', '.') }}</td>
                    <td class="text-right credit-text">Rp {{ number_format($report->total_credit, 0, ',', '.') }}</td>
                </tr>
                <tr class="balance-row">
                    <td colspan="2" class="text-right uppercase" style="letter-spacing: 1px;">Saldo Akhir</td>
                    <td colspan="2" class="text-right">
                        {{-- Highlight Saldo Akhir --}}
                        <span class="balance-amount">
                            Rp {{ number_format($report->balance, 0, ',', '.') }}
                        </span>
                    </td>
                </tr>
            </tfoot>
        </table>

        {{-- TANDA TANGAN --}}
        <div class="signature-section">
            <table style="width: 100%; border: none;">
                <tr style="border: none;">
                    <td style="width: 60%;"></td> 
                    <td style="width: 40%; text-align: center;">
                        <p style="margin-bottom: 5px; color: #64748b; font-weight: 600;">Dibuat Oleh,</p>
                        <br><br><br><br>
                        <span class="signature-line">
                            {{ $report->creator_name }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- LAMPIRAN (MODERN CARD LAYOUT) --}}
        @php
            
            $useStoragePath = false; 

            $entriesWithImages = $report->entries->whereNotNull('receipt_image_path');
        @endphp

        @if ($entriesWithImages->isNotEmpty())
            <div class="page-break"></div> 
            
            <h2 class="gallery-header">Lampiran Bukti Transaksi</h2>

            <table class="gallery-table"> 
                @foreach ($entriesWithImages->chunk(2) as $row)
                    <tr>
                        @foreach ($row as $entry)
                            <td class="gallery-td">
                                {{-- Card Modern --}}
                                <div class="nota-card">
                                    <div class="nota-card-header">
                                        <h4 class="nota-title">{{ $entry->description }}</h4>
                                    </div>

                                    <div class="nota-body">
                                        @php
                                            $imgSrc = $useStoragePath 
                                                ? storage_path('app/public/' . $entry->receipt_image_path) 
                                                : public_path('storage/' . $entry->receipt_image_path);
                                        @endphp
                                        {{-- Gambar --}}
                                        <img src="{{ $imgSrc }}" class="nota-image" alt="Bukti">
                                    </div>
                                    
                                    <div class="nota-footer text-center">
                                        <span class="nota-badge">
                                            {{ ucfirst($entry->type) }}: Rp {{ number_format($entry->amount, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                        @endforeach

                        @if($row->count() < 2)
                            <td class="gallery-td"></td>
                        @endif
                    </tr>
                @endforeach
            </table>
        @endif
        
    </div>
</body>
</html>