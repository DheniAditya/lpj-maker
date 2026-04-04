<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>LPJ - {{ $report->title }}</title>
    <style>
        @page { margin: 2.5cm 2cm; size: A4 portrait; }
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #334155; line-height: 1.5; background-color: #fff; }
        .container { width: 100%; margin: 0 auto; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .uppercase { text-transform: uppercase; }

        /* Header Section */
        .header-section { text-align: center; border-bottom: 2px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 25px; }
        .report-label { font-size: 10px; font-weight: 600; color: #64748b; display: block; margin-bottom: 5px; }
        .report-title { font-size: 24px; font-weight: 800; color: #1e293b; margin: 0; }

        /* Table Style */
        table.main-table { width: 100%; border-collapse: collapse; margin-top: 20px; border: 1px solid #e2e8f0; }
        .main-table thead th { background-color: #1e293b; color: #ffffff; padding: 12px; text-align: left; }
        .main-table tbody td { padding: 10px; border-bottom: 1px solid #e2e8f0; }
        .total-row td { background-color: #f8fafc; font-weight: bold; border-top: 2px solid #cbd5e1; }
        .balance-row td { background-color: #1e293b; color: #ffffff; font-weight: 800; font-size: 13px; padding: 14px 10px; }
        
        .debit-text { color: #16a34a; }
        .credit-text { color: #dc2626; }

        /* Signature */
        .signature-section { margin-top: 40px; }
        .signature-line { border-bottom: 1px solid #334155; display: inline-block; font-weight: bold; min-width: 150px; padding-top: 50px; }

        /* Gallery/Nota */
        .page-break { page-break-after: always; }
        .gallery-header { font-size: 16px; font-weight: bold; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 20px; }
        .gallery-table { width: 100%; border-collapse: collapse; }
        .gallery-td { width: 50%; padding: 8px; vertical-align: top; }
        .nota-box { border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px; text-align: center; background-color: #f8fafc; }
        .nota-image { max-width: 100%; max-height: 220px; border-radius: 4px; margin: 8px 0; }
        .nota-title { font-weight: bold; display: block; color: #1e293b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-section">
            <span class="report-label uppercase">Lembar Pertanggungjawaban Anggaran</span>
            <h1 class="report-title">{{ $report->title }}</h1>
            <div style="margin-top: 5px; color: #94a3b8;">Dicetak pada: {{ date('d F Y') }}</div>
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 15%;" class="text-center">Tanggal</th>
                    <th style="width: 45%;">Keterangan</th>
                    <th style="width: 20%;" class="text-right">Debit</th>
                    <th style="width: 20%;" class="text-right">Kredit</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($report->entries as $entry)
                <tr>
                    <td class="text-center">{{ $entry->created_at->format('d/m/Y') }}</td>
                    <td>{{ $entry->description }}</td>
                    <td class="text-right {{ $entry->type == 'debit' ? 'debit-text' : '' }}">
                        {{ $entry->type == 'debit' ? 'Rp ' . number_format($entry->amount, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right {{ $entry->type == 'credit' ? 'credit-text' : '' }}">
                        {{ $entry->type == 'credit' ? 'Rp ' . number_format($entry->amount, 0, ',', '.') : '-' }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="2" class="text-right">TOTAL MUTASI</td>
                    <td class="text-right debit-text">Rp {{ number_format($report->total_debit, 0, ',', '.') }}</td>
                    <td class="text-right credit-text">Rp {{ number_format($report->total_credit, 0, ',', '.') }}</td>
                </tr>
                <tr class="balance-row">
                    <td colspan="2" class="text-right">SISA SALDO AKHIR</td>
                    <td colspan="2" class="text-right">Rp {{ number_format($report->balance, 0, ',', '.') }}</td>
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

     {{-- BAGIAN LAMPIRAN ANTI-GAGAL --}}
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
                                // 1. Coba susun path fisik (sesuai URL localhost kamu)
                                // Kita hapus string 'storage/' jika di database sudah ada, 
                                // lalu kita rakit manual ke folder storage/app/public
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
                                <div style="border: 1px dashed red; padding: 10px; color: red; font-size: 9px;">
                                    Gambar Tidak Terbaca di Server<br>
                                    Path dicari: <br>
                                    <code style="font-size: 7px;">{{ $fullPath }}</code>
                                </div>
                            @endif

                            <div style="font-size: 10px; color: #64748b; margin-top: 5px;">
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