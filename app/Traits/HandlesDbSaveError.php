<?php

namespace App\Traits;

use Illuminate\Database\QueryException;
use Throwable;

trait HandlesDbSaveError
{
    /**
     * @param array<string,string> $overrides Custom message per SQLSTATE code, e.g. ['23505' => '...'].
     */
    protected function dbErrorMessage(Throwable $e, string $prefix, array $overrides = []): string
    {
        if ($e instanceof QueryException) {
            $defaults = [
                '23505' => 'Data yang sama sudah terdaftar, silakan periksa kembali data yang dimasukkan.',
                '23000' => 'Data yang sama sudah terdaftar, silakan periksa kembali data yang dimasukkan.',
                '23503' => 'Data terkait tidak ditemukan atau masih digunakan oleh data lain.',
                '23502' => 'Ada kolom wajib yang belum diisi.',
            ];

            $code = $e->getCode();
            return $overrides[$code] ?? $defaults[$code] ?? $prefix . ': Terjadi kesalahan pada database, silakan hubungi admin.';
        }

        return $prefix . ': ' . $e->getMessage();
    }
}
