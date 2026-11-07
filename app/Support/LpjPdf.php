<?php

namespace App\Support;

use App\Models\ExpenseReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfWrapper;
use Dompdf\Dompdf;

/**
 * Membuat PDF LPJ (desain sama dengan LPJ Maker versi publik):
 * font IBM Plex didaftarkan dulu, lalu nomor halaman "page x/y" ditulis setelah render.
 */
class LpjPdf
{
    /** [keluarga, tebal, gaya, file di public/fonts] */
    private const FONTS = [
        ['Plex', 'normal', 'normal', 'IBMPlexSans-Regular.ttf'],
        ['Plex', 'bold', 'normal', 'IBMPlexSans-SemiBold.ttf'],
        ['Plex', 'normal', 'italic', 'IBMPlexSans-Italic.ttf'],
        ['PlexMono', 'normal', 'normal', 'IBMPlexMono-Regular.ttf'],
        ['PlexMono', 'bold', 'normal', 'IBMPlexMono-SemiBold.ttf'],
    ];

    public static function make(ExpenseReport $report): DomPdfWrapper
    {
        $report->loadMissing('entries.images');

        $pdf = Pdf::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'chroot' => public_path(),
        ], true);

        $dompdf = $pdf->getDomPDF();
        self::registerFonts($dompdf);

        $pdf->loadView('lpj.pdf_template', compact('report'));
        $pdf->render();
        self::addPageNumbers($dompdf);

        return $pdf;
    }

    /**
     * Daftarkan font sebelum render. Tanpa ini, render pertama (cache font masih kosong)
     * memakai Helvetica/Courier untuk teks tebal dan angka.
     */
    private static function registerFonts(Dompdf $dompdf): void
    {
        $metrics = $dompdf->getFontMetrics();
        $installed = $metrics->getFontFamilies();

        foreach (self::FONTS as [$family, $weight, $style, $file]) {
            $key = $weight === 'bold' ? ($style === 'italic' ? 'bold_italic' : 'bold') : ($style === 'italic' ? 'italic' : 'normal');

            if (isset($installed[strtolower($family)][$key]) && is_file($installed[strtolower($family)][$key].'.ufm.json')) {
                continue;
            }

            $metrics->registerFont(['family' => $family, 'weight' => $weight, 'style' => $style], public_path('fonts/'.$file));
        }
    }

    private static function addPageNumbers(Dompdf $dompdf): void
    {
        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->getFont('PlexMono');
        $size = 9.5;
        $text = 'page {PAGE_NUM}/{PAGE_COUNT}';
        $width = $dompdf->getFontMetrics()->getTextWidth('page 1/1', $font, $size);

        // A4 = 595.28 × 841.89 pt; teks di tengah, 12 mm dari bawah.
        $canvas->page_text(($canvas->get_width() - $width) / 2, $canvas->get_height() - 12 * 2.835 - $size, $text, $font, $size, [0.067, 0.067, 0.067]);
    }
}
