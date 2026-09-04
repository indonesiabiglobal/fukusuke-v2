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
     * File JS memuat library lewat script.src bikinan JS atau document.write, bukan
     * tag <script> di blade — lolos dari pemeriksaan di atas. Dua kasus nyata yang
     * pernah lolos: CDN qrcode di public/js, dan CDN toastify di resources/js/plugins.js
     * yang jalan di SETIAP halaman.
     */
    public function test_no_javascript_source_loads_libraries_from_the_public_internet(): void
    {
        $root = dirname(__DIR__, 2);
        $offenders = [];

        $paths = array_merge(
            glob($root . '/public/js/*.js'),
            $this->jsFilesIn($root . '/resources/js')
        );

        foreach ($paths as $path) {
            $relative = str_replace(chr(92), '/', substr($path, strlen($root) + 1));

            foreach (explode("\n", file_get_contents($path)) as $i => $line) {
                // .src = "https://..."  atau  document.write("<script src='https://...'>")
                if (preg_match('/(?:\.src\s*=\s*|src\s*=\s*\\\\?[\'"])[\'"]?(https?:\/\/[^\'"\s>]+)/i', $line, $m)) {
                    $offenders[] = sprintf('%s:%d -> %s', $relative, $i + 1, $m[1]);
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

    /**
     * @import url(https://...) di SCSS ikut terkompilasi ke app.min.css dan bersifat
     * render-blocking — tidak terlihat sebagai tag di blade maupun sebagai script.
     * Ini yang menyembunyikan Google Fonts (Gantari + Poppins).
     */
    public function test_no_stylesheet_imports_from_the_public_internet(): void
    {
        $root = dirname(__DIR__, 2);
        $offenders = [];

        $files = new \RegexIterator(
            new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root . '/resources/scss')),
            '/\.scss$/'
        );

        foreach ($files as $file) {
            $relative = str_replace(chr(92), '/', substr($file->getPathname(), strlen($root) + 1));

            foreach (explode("\n", file_get_contents($file->getPathname())) as $i => $line) {
                if (preg_match('/@import\s+url\(\s*[\'"]?(https?:\/\/[^\'")]+)/i', $line, $m)) {
                    $offenders[] = sprintf('%s:%d -> %s', $relative, $i + 1, $m[1]);
                }
            }
        }

        sort($offenders);

        $this->assertSame(
            [],
            $offenders,
            "Stylesheet berikut di-@import dari internet publik dan memblokir render:\n  "
            . implode("\n  ", $offenders)
        );
    }

    /**
     * Bundle datatables memaketkan jQuery-nya sendiri dan mengganti window.jQuery,
     * sehingga plugin jQuery yang didaftarkan lebih dulu ikut hilang. Pernah terjadi:
     * select2 dipindah ke atas datatables dan semua dropdown mati.
     */
    public function test_jquery_plugins_are_loaded_after_the_datatables_bundle(): void
    {
        $source = file_get_contents(
            dirname(__DIR__, 2) . '/resources/views/layouts/vendor-scripts.blade.php'
        );
        $source = $this->stripBladeComments($source);

        $datatables = strpos($source, 'datatables/js/datatables.min.js');
        $select2 = strpos($source, 'select2/js/select2.min.js');

        $this->assertNotFalse($datatables, 'Bundle datatables tidak ditemukan di vendor-scripts.');
        $this->assertNotFalse($select2, 'select2 tidak ditemukan di vendor-scripts.');

        $this->assertGreaterThan(
            $datatables,
            $select2,
            'select2 dimuat SEBELUM bundle datatables. Bundle itu mengganti window.jQuery '
            . '(memaketkan jQuery 3.7.0 sendiri), jadi pendaftaran select2 terhapus dan '
            . 'semua dropdown mati. Muat select2 setelah datatables.'
        );
    }

    /** @return list<string> */
    private function jsFilesIn(string $dir): array
    {
        if (! is_dir($dir)) {
            return [];
        }

        $found = [];
        $files = new \RegexIterator(
            new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir)),
            '/\.js$/'
        );

        foreach ($files as $file) {
            $found[] = $file->getPathname();
        }

        sort($found);

        return $found;
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
