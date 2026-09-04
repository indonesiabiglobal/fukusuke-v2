<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * GeneralReportController membungkus setiap export dengan response()->streamDownload()
 * lalu memanggil $response['writer']->save('php://output').
 *
 * Jadi setiap service yang mengembalikan status 'success' WAJIB menyertakan 'writer'.
 * Satu method yang tertinggal pola lama (menyimpan file lalu mengembalikan 'filename'
 * saja) menghasilkan "Undefined array key: writer" — dan karena itu terjadi di dalam
 * closure streamDownload, error-nya muncul setelah header terkirim.
 */
class ReportExportContractTest extends TestCase
{
    public function test_every_successful_report_response_carries_a_writer(): void
    {
        $root = dirname(__DIR__, 2);
        $offenders = [];

        foreach ($this->reportSources($root) as $path) {
            $relative = str_replace(chr(92), '/', substr($path, strlen($root) + 1));
            $source = file_get_contents($path);

            // Setiap literal array yang memuat status 'success'.
            preg_match_all('/\[[^\[\]]*?[\'"]status[\'"]\s*=>\s*[\'"]success[\'"][^\[\]]*?\]/s', $source, $matches, PREG_OFFSET_CAPTURE);

            foreach ($matches[0] as [$literal, $offset]) {
                if (str_contains($literal, "'writer'") || str_contains($literal, '"writer"')) {
                    continue;
                }

                $line = substr_count(substr($source, 0, $offset), "\n") + 1;
                $offenders[] = sprintf('%s:%d', $relative, $line);
            }
        }

        sort($offenders);

        $this->assertSame(
            [],
            $offenders,
            "Response 'success' berikut tidak menyertakan 'writer'. GeneralReportController akan\n"
            . "gagal dengan \"Undefined array key: writer\" di dalam closure streamDownload:\n  "
            . implode("\n  ", $offenders)
        );
    }

    /**
     * Menyimpan ke path relatif menulis ke current working directory, yang di bawah
     * PHP-FPM adalah public/ — file laporan menumpuk di web root dan bisa diunduh
     * siapa pun yang menebak URL-nya.
     */
    public function test_no_report_service_writes_spreadsheets_to_disk(): void
    {
        $root = dirname(__DIR__, 2);
        $offenders = [];

        foreach ($this->reportSources($root) as $path) {
            $relative = str_replace(chr(92), '/', substr($path, strlen($root) + 1));

            foreach (explode("\n", file_get_contents($path)) as $i => $line) {
                if (preg_match('/\$writer\s*->\s*save\(\s*(?![\'"]php:\/\/)/', $line)) {
                    $offenders[] = sprintf('%s:%d -> %s', $relative, $i + 1, trim($line));
                }
            }
        }

        sort($offenders);

        $this->assertSame(
            [],
            $offenders,
            "Spreadsheet disimpan ke disk, bukan dialirkan ke php://output:\n  "
            . implode("\n  ", $offenders)
        );
    }

    /** @return list<string> */
    private function reportSources(string $root): array
    {
        $found = glob($root . '/app/Http/Livewire/Report/GeneralReport/*.php') ?: [];
        sort($found);

        return $found;
    }
}
