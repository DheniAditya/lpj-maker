<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LPJ - {{ $report->title }}</title>
    <style>
        @page { margin: 2.5cm 1.5cm; size: A4 portrait; }
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #334155; line-height: 1.5; background-color: #fff; }
        .container { width: 100%; margin: 0 auto; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .uppercase { text-transform: uppercase; }

        /* Header Section */
        .header-section { text-align: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px; }
        .report-label { font-size: 9px; font-weight: 600; color: #64748b; display: block; margin-bottom: 5px; }
        .report-title { font-size: 20px; font-weight: 800; color: #1e293b; margin: 0; }

        /* Table Style */
        table.main-table { width: 100%; border-collapse: collapse; margin-top: 10px; border: 1px solid #cbd5e1; table-layout: fixed; }
        .main-table thead th { background-color: #1e293b; color: #e2e8f0; padding: 10px 5px; text-align: center; font-size: 12px; border: 1px solid #334155; }
        .main-table tbody td { padding: 10px 5px; border: 1px solid #e2e8f0; vertical-align: middle; word-wrap: break-word; }
        
        /* Padding khusus angka agar tidak menempel border kanan */
        .pr-10 { padding-right: 10px !important; }

        .total-row td { background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 10px 5px; }
        .balance-row td { background-color: #1e293b; color: #ffffff; padding: 12px 10px; }
        
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
            @php
                $imageCount = $entry->images ? $entry->images->count() : 0;
            @endphp

            @if($imageCount > 0)
                <span style="color: #64748b; font-size: 10px;">
                    Terlampir ({{ $imageCount }})
                </span>
            @else
                <span style="color: #64748b; font-size: 10px;">Tidak Ada</span>
            @endif
        </td>
        <td class="text-right pr-10 {{ $entry->type == 'debit' ? 'debit-text' : '' }}">
            {{ $entry->type == 'debit' ? 'Rp ' . number_format($entry->amount, 0, ',', '.') : '-' }}
        </td>
        <td class="text-right pr-10 {{ $entry->type == 'credit' ? 'credit-text' : '' }}">
            {{ $entry->type == 'credit' ? 'Rp ' . number_format($entry->amount, 0, ',', '.') : '-' }}
        </td>
    </tr>
    @empty
    <tr><td colspan="5" class="text-center">Tidak ada data entri.</td></tr>
    @endforelse
</tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" class="text-center pr-10">TOTAL MUTASI</td>
                    <td class="text-center pr-10 debit-text">Rp {{ number_format($report->total_debit, 0, ',', '.') }}</td>
                    <td class="text-center pr-10 credit-text">Rp {{ number_format($report->total_credit, 0, ',', '.') }}</td>
                </tr>
                <tr class="balance-row">
                    <td colspan="3" class="text-center pr-10 " style="font-weight: bold; text-transform: uppercase; font-family:'Times New Roman', Times, serif;  font-size: 14px;">Saldo Akhir</td>
                    <td colspan="2" class="text-center pr-10 {{ $report->balance < 0 ? 'credit-text' : 'debit-text' }}" style="font-weight: 800; font-size: 14px;">
    Rp {{ number_format($report->balance, 0, ',', '.') }}
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

        {{-- HALAMAN LAMPIRAN NOTA --}}
        @php
            $entriesWithImages = $report->entries->filter(function($entry) {
                return $entry->images && $entry->images->count() > 0;
            });
        @endphp

        @if ($entriesWithImages->isNotEmpty())
            <div class="page-break"></div>
            <h2 class="gallery-header">Lampiran Bukti Transaksi</h2>
            
            <table class="gallery-table">
                @php 
                    $allImages = collect();
                    foreach($entriesWithImages as $entry) {
                        foreach($entry->images as $img) {
                            $allImages->push([
                                'path' => $img->image_path,
                                'desc' => $entry->description,
                                'amount' => $entry->amount
                            ]);
                        }
                    }
                @endphp

                @foreach ($allImages->chunk(2) as $row)
                    <tr>
                        @foreach ($row as $item)
                            <td class="gallery-td">
                                <div class="nota-box">
                                    <span class="nota-title">{{ $item['desc'] }}</span>
                                    
                                    @php
                                        $cleanPath = str_replace('storage/', '', $item['path']);
                                        $fullPath = storage_path('app/public/' . $cleanPath);
                                        $base64 = null;
                                        if (file_exists($fullPath)) {
                                            $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
                                            $fileData = file_get_contents($fullPath);
                                            $base64 = 'data:image/' . $extension . ';base64,' . base64_encode($fileData);
                                        }
                                    @endphp
                                    
                                    @if($base64)
                                        <img src="{{ $base64 }}" class="nota-image">
                                    @else
                                        <div style="border: 1px dashed #cbd5e1; padding: 30px 10px; color: #94a3b8; font-size: 10px;">
                                            Gambar tidak ditemukan fisik<br>
                                            <span style="font-size: 6px;">{{ $item['path'] }}</span>
                                        </div>
                                    @endif

                                    <div style="font-size: 10px; color: #475569; margin-top: 5px; border-top: 1px solid #e2e8f0; padding-top: 5px;">
                                        Rp {{ number_format($item['amount'], 0, ',', '.') }}
                                    </div>
                                </div>
                            </td>
                        @endforeach
                        @if($row->count() < 2) <td class="gallery-td"></td> @endif
                    </tr>
                @endforeach
            </table>
        @endif
    </div>
</body>
</html>