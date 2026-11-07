<?php

namespace App\Support;

/**
 * Angka ke kata dalam Bahasa Indonesia (Title Case), contoh 7735000 → "Tujuh Juta Tujuh Ratus Tiga Puluh Lima Ribu".
 */
class Terbilang
{
    private const SATUAN = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];

    public static function make(int|float $number): string
    {
        $n = (int) floor(abs($number));

        return $n === 0 ? 'Nol' : self::words($n);
    }

    /** "Minus Empat Ribu Rupiah" untuk nilai negatif. */
    public static function rupiah(int|float $number): string
    {
        return ($number < 0 ? 'Minus ' : '').self::make($number).' Rupiah';
    }

    private static function words(int $x): string
    {
        $join = fn (string $head, int $rest) => $head.($rest ? ' '.self::words($rest) : '');

        return match (true) {
            $x < 12 => self::SATUAN[$x],
            $x < 20 => self::words($x - 10).' Belas',
            $x < 100 => $join(self::words(intdiv($x, 10)).' Puluh', $x % 10),
            $x < 200 => $join('Seratus', $x - 100),
            $x < 1000 => $join(self::words(intdiv($x, 100)).' Ratus', $x % 100),
            $x < 2000 => $join('Seribu', $x - 1000),
            $x < 1_000_000 => $join(self::words(intdiv($x, 1000)).' Ribu', $x % 1000),
            $x < 1_000_000_000 => $join(self::words(intdiv($x, 1_000_000)).' Juta', $x % 1_000_000),
            $x < 1_000_000_000_000 => $join(self::words(intdiv($x, 1_000_000_000)).' Miliar', $x % 1_000_000_000),
            default => $join(self::words(intdiv($x, 1_000_000_000_000)).' Triliun', $x % 1_000_000_000_000),
        };
    }
}
