<?php

namespace App\Helpers;

class Terbilang
{
    /**
     * Konversi angka menjadi teks terbilang (Bahasa Indonesia).
     *
     * @param  float|int  $angka
     * @return string
     */
    public static function convert($angka): string
    {
        $angka = abs($angka);
        $huruf = [
            '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima',
            'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas',
        ];

        if ($angka < 12) {
            return ' ' . $huruf[$angka];
        }

        if ($angka < 20) {
            return self::convert($angka - 10) . ' Belas';
        }

        if ($angka < 100) {
            return self::convert(intval($angka / 10)) . ' Puluh' . self::convert($angka % 10);
        }

        if ($angka < 200) {
            return ' Seratus' . self::convert($angka - 100);
        }

        if ($angka < 1000) {
            return self::convert(intval($angka / 100)) . ' Ratus' . self::convert($angka % 100);
        }

        if ($angka < 2000) {
            return ' Seribu' . self::convert($angka - 1000);
        }

        if ($angka < 1000000) {
            return self::convert(intval($angka / 1000)) . ' Ribu' . self::convert($angka % 1000);
        }

        if ($angka < 1000000000) {
            return self::convert(intval($angka / 1000000)) . ' Juta' . self::convert($angka % 1000000);
        }

        if ($angka < 1000000000000) {
            return self::convert(intval($angka / 1000000000)) . ' Miliar' . self::convert($angka % 1000000000);
        }

        if ($angka < 1000000000000000) {
            return self::convert(intval($angka / 1000000000000)) . ' Triliun' . self::convert($angka % 1000000000000);
        }

        return '';
    }

    /**
     * Shortcut: langsung trim & format rapi.
     *
     * @param  float|int  $angka
     * @return string
     */
    public static function make($angka): string
    {
        return trim(self::convert($angka));
    }
}
