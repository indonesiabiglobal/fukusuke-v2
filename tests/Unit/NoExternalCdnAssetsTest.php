<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Aplikasi ini dipakai di LAN pabrik yang sering tanpa akses internet.
 *
 * <script src> dan <link href> ke CDN publik bersifat blocking: tanpa internet
 * browser menunggu DNS timeout (~30 detik) sebelum halaman tampil. Semua library
 * harus di-vendor ke resources/libs/ (disalin Vite ke public/build/libs/) dan
 * dirujuk lewat URL::asset('build/libs/...').
 */
class NoExternalCdnAssetsTest extends TestCase
{
    /**
     * Halaman demo bawaan tema Themesbrand + template email.
     * Bukan bagian alur produksi; email dirender di mail client yang punya internet.
     */
    private const ALLOWED = [
        'resources/views/emails/',
        'resources/views/welcome.blade.php',
        'resources/views/index.blade.php',
        'resources/views/widgets.blade.php',
        'resources/views/maps-google.blade.php',
        'resources/views/tables-datatables.blade.php',
        'resources/views/charts-apex-area.blade.php',
        'resources/views/charts-apex-candlestick.blade.php',
        'resources/views/charts-apex-column.blade.php',
        'resources/views/charts-apex-line.blade.php',
    ];

    public function test_no_blade_view_loads_blocking_assets_from_the_public_internet(): void
    {
        $root = dirname(__DIR__, 2);
        $offenders = [];

        $files = new \RegexIterator(
            new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($root . '/resources/views')
            ),
            '/\.blade\.php$/'
        );

        foreach ($files as $file) {
            $relative = str_replace(chr(92), '/', substr($file->getPathname(), strlen($root) + 1));

            foreach (self::ALLOWED as $allowed) {
                if (str_starts_with($relative, $allowed)) {
                    continue 2;
                }
            }

            $source = $this->stripBladeComments(file_get_contents($file->getPathname()));

            foreach (explode("\n", $source) as $i => $line) {
                if (preg_match('/<(?:script[^>]*\ssrc|link[^>]*\shref)\s*=\s*"(https?:\/\/[^"]+)"/i', $line, $m)) {
                    $offenders[] = sprintf('%s:%d -> %s', $relative, $i + 1, $m[1]);
                }
            }
        }

        sort($offenders);

        $this->assertSame(
            [],
            $offenders,
            "Aset berikut ditarik dari internet publik dan akan memblokir render saat LAN offline.\n"
            . "Vendor file-nya ke resources/libs/ lalu rujuk via URL::asset('build/libs/...'):\n  "
            . implode("\n  ", $offenders)
        );
    }

    /**
     * public/js/ memuat library lewat script.src bikinan JS, bukan tag <script> —
     * lolos dari pemeriksaan blade di atas. Ini yang menyembunyikan CDN qrcode.
     */
    public function test_no_public_js_loads_libraries_from_the_public_internet(): void
    {
        $root = dirname(__DIR__, 2);
        $offenders = [];

        foreach (glob($root . '/public/js/*.js') as $path) {
            foreach (explode("\n", file_get_contents($path)) as $i => $line) {
                if (preg_match('/\.src\s*=\s*[\'"](https?:\/\/[^\'"]+)[\'"]/i', $line, $m)) {
                    $offenders[] = sprintf('public/js/%s:%d -> %s', basename($path), $i + 1, $m[1]);
                }
            }
        }

        sort($offenders);

        $this->assertSame(
            [],
            $offenders,
            "Script berikut dimuat dari internet publik saat runtime:\n  "
            . implode("\n  ", $offenders)
        );
    }

    /** Kosongkan isi {{-- --}} tapi pertahankan jumlah baris agar nomor baris tetap akurat. */
    private function stripBladeComments(string $source): string
    {
        return preg_replace_callback(
            '/\{\{--.*?--\}\}/s',
            fn (array $m) => str_repeat("\n", substr_count($m[0], "\n")),
            $source
        );
    }
}
