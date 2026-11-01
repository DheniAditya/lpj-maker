<?php

namespace App\Support;

use GdImage;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

/**
 * Mengolah gambar tanda tangan menjadi PNG transparan yang rapi:
 *  1. Diperkecil (maks 1200 px) supaya ringan.
 *  2. Jika gambar belum transparan (mis. foto/scan di kertas putih),
 *     warna latar dideteksi dari tepi gambar lalu dibuat transparan.
 *  3. Area kosong di sekeliling tanda tangan dipotong.
 */
class SignatureImage
{
    private const MAX_SIDE = 1200;

    private const PADDING = 12;

    public static function fromUpload(UploadedFile $file): string
    {
        return self::process((string) file_get_contents($file->getRealPath()));
    }

    /** Data URL dari canvas, mis. "data:image/png;base64,...". */
    public static function fromDataUrl(string $dataUrl): string
    {
        if (! preg_match('#^data:image/(png|jpe?g|webp);base64,(.+)$#', $dataUrl, $match)) {
            throw new InvalidArgumentException('Format tanda tangan tidak dikenali.');
        }

        $binary = base64_decode($match[2], true);
        if ($binary === false) {
            throw new InvalidArgumentException('Data tanda tangan rusak.');
        }

        return self::process($binary);
    }

    /** @return string PNG binary */
    public static function process(string $binary): string
    {
        $source = @imagecreatefromstring($binary);
        if (! $source instanceof GdImage) {
            throw new InvalidArgumentException('File bukan gambar yang valid.');
        }

        $image = self::toTrueColorAlpha(self::scaleDown($source));

        if (! self::hasTransparency($image)) {
            self::removeBackground($image);
        }

        $image = self::trim($image);

        ob_start();
        imagepng($image, null, 6);

        return (string) ob_get_clean();
    }

    private static function scaleDown(GdImage $image): GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $ratio = self::MAX_SIDE / max($width, $height);

        if ($ratio >= 1) {
            return $image;
        }

        return imagescale($image, (int) round($width * $ratio), (int) round($height * $ratio));
    }

    private static function toTrueColorAlpha(GdImage $image): GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);

        $canvas = imagecreatetruecolor($width, $height);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));
        imagealphablending($canvas, true);
        imagecopy($canvas, $image, 0, 0, 0, 0, $width, $height);
        imagealphablending($canvas, false);

        return $canvas;
    }

    /** Dianggap sudah transparan jika cukup banyak piksel yang alpha-nya tidak penuh. */
    private static function hasTransparency(GdImage $image): bool
    {
        [$transparent, $total] = [0, 0];

        foreach (self::samplePoints($image, 4000) as [$x, $y]) {
            $total++;
            if (((imagecolorat($image, $x, $y) >> 24) & 0x7F) > 100) {
                $transparent++;
            }
        }

        return $total > 0 && $transparent / $total > 0.05;
    }

    /**
     * Latar = rata-rata kecerahan piksel tepi. Piksel yang mendekati latar dibuat
     * transparan; piksel tinta dibuat makin pekat sesuai selisihnya (tepi tetap halus).
     */
    private static function removeBackground(GdImage $image): void
    {
        $width = imagesx($image);
        $height = imagesy($image);

        $edges = [];
        for ($x = 0; $x < $width; $x += max(1, intdiv($width, 100))) {
            $edges[] = self::luminance(imagecolorat($image, $x, 0));
            $edges[] = self::luminance(imagecolorat($image, $x, $height - 1));
        }
        for ($y = 0; $y < $height; $y += max(1, intdiv($height, 100))) {
            $edges[] = self::luminance(imagecolorat($image, 0, $y));
            $edges[] = self::luminance(imagecolorat($image, $width - 1, $y));
        }
        sort($edges);
        $background = $edges[intdiv(count($edges), 2)]; // median, tahan terhadap bayangan

        $start = $background - 25;   // lebih terang dari ini = latar
        $full = $background - 110;   // lebih gelap dari ini = tinta penuh

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgb = imagecolorat($image, $x, $y);
                $lum = self::luminance($rgb);

                if ($lum >= $start) {
                    imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, 0, 0, 0, 127));

                    continue;
                }

                $strength = min(1, ($start - $lum) / max(1, $start - $full));
                $alpha = (int) round(127 * (1 - $strength));
                $color = imagecolorallocatealpha($image, ($rgb >> 16) & 0xFF, ($rgb >> 8) & 0xFF, $rgb & 0xFF, $alpha);
                imagesetpixel($image, $x, $y, $color);
            }
        }
    }

    /** Potong area transparan di sekeliling tanda tangan. */
    private static function trim(GdImage $image): GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        [$minX, $minY, $maxX, $maxY] = [$width, $height, -1, -1];

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if (((imagecolorat($image, $x, $y) >> 24) & 0x7F) < 110) {
                    $minX = min($minX, $x);
                    $maxX = max($maxX, $x);
                    $minY = min($minY, $y);
                    $maxY = max($maxY, $y);
                }
            }
        }

        if ($maxX < 0) {
            throw new InvalidArgumentException('Tanda tangan kosong atau tidak terbaca. Coba gambar yang lebih jelas.');
        }

        $minX = max(0, $minX - self::PADDING);
        $minY = max(0, $minY - self::PADDING);
        $cropWidth = min($width, $maxX + self::PADDING + 1) - $minX;
        $cropHeight = min($height, $maxY + self::PADDING + 1) - $minY;

        $cropped = imagecreatetruecolor($cropWidth, $cropHeight);
        imagealphablending($cropped, false);
        imagesavealpha($cropped, true);
        imagecopy($cropped, $image, 0, 0, $minX, $minY, $cropWidth, $cropHeight);

        return $cropped;
    }

    private static function luminance(int $rgb): float
    {
        return 0.299 * (($rgb >> 16) & 0xFF) + 0.587 * (($rgb >> 8) & 0xFF) + 0.114 * ($rgb & 0xFF);
    }

    /** @return iterable<array{int, int}> */
    private static function samplePoints(GdImage $image, int $count): iterable
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $step = max(1, (int) floor(sqrt(($width * $height) / $count)));

        for ($y = 0; $y < $height; $y += $step) {
            for ($x = 0; $x < $width; $x += $step) {
                yield [$x, $y];
            }
        }
    }
}
