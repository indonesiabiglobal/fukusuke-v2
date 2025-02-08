<?php

namespace App\Helpers;

class FormatAngka
{
    public static function rupiah($angka)
    {
        $hasil_rupiah = "Rp " . number_format($angka, 0, ',', '.');
        return $hasil_rupiah;
    }

    public static function ribuanCetak($number)
    {
        $result = number_format($number, 0, ',', '.');
        return $result;
    }

    public static function ribuan($number)
    {
        $result = number_format($number, 0, '.', ',');
        return $result;
    }
}
