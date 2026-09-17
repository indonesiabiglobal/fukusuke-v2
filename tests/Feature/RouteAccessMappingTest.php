<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RouteAccessMappingTest extends TestCase
{
    /** route name => required access code(s) (OR'd together, matching the ->middleware('access:A,B') syntax) */
    private const EXPECTED = [
        'order-lpk' => ['ORDER'],
        'edit-order' => ['ORDER'],
        'add-order' => ['ORDER'],
        'lpk-entry' => ['ORDER'],
        'add-lpk' => ['ORDER'],
        'edit-lpk' => ['ORDER'],
        'cetak-lpk' => ['ORDER'],
        'order-report' => ['ORDER'],
        'report-lpk' => ['ORDER'],
        'cetak-order' => ['ORDER'],

        'nippo-infure' => ['NIPPO-INFURE'],
        'edit-nippo' => ['NIPPO-INFURE'],
        'add-nippo' => ['NIPPO-INFURE'],
        'get-print-data' => ['NIPPO-INFURE'],
        'loss-infure' => ['NIPPO-INFURE'],
        'checklist-infure' => ['NIPPO-INFURE'],
        'label-gentan' => ['NIPPO-INFURE'],
        'nippo-infure-print' => ['NIPPO-INFURE'],
        'report-nippo-infure' => ['NIPPO-INFURE'],
        'report-gentan' => ['NIPPO-INFURE'],

        'nippo-seitai' => ['NIPPO-SEITAI'],
        'add-seitai' => ['NIPPO-SEITAI'],
        'edit-seitai' => ['NIPPO-SEITAI'],
        'loss-seitai' => ['NIPPO-SEITAI'],
        'add-loss' => ['NIPPO-SEITAI'],
        'mutasi-isi-palet' => ['NIPPO-SEITAI'],
        'check-list-seitai' => ['NIPPO-SEITAI'],
        'label-masuk-gudang' => ['NIPPO-SEITAI'],
        'report-masuk-gudang' => ['NIPPO-SEITAI'],
        'report-checklist-seitai' => ['NIPPO-SEITAI'],
        'report-loss-seitai' => ['NIPPO-SEITAI'],

        'infure-jam-kerja' => ['JAM-KERJA-INFURE'],
        'seitai-jam-kerja' => ['JAM-KERJA-SEITAI'],
        'checklist-jam-kerja' => ['JAM-KERJA-INFURE', 'JAM-KERJA-SEITAI'],

        'kenpin-infure' => ['KENPIN'],
        'add-kenpin-infure' => ['KENPIN'],
        'edit-kenpin-infure' => ['KENPIN'],
        'kenpin-seitai-kenpin' => ['KENPIN'],
        'add-kenpin-seitai' => ['KENPIN'],
        'edit-kenpin-seitai' => ['KENPIN'],
        'mutasi-isi-palet-kenpin' => ['KENPIN'],
        'print-label-gudang-kenpin' => ['KENPIN'],
        'report-kenpin' => ['KENPIN'],

        'penarikan-palet' => ['WAREHOUSE'],
        'pengembalian-palet' => ['WAREHOUSE'],
        'label-masuk-gudang-report' => ['WAREHOUSE'],

        'general-report' => ['REPORT'],
        'detail-report' => ['REPORT'],
        'production-loss-report' => ['REPORT'],

        'buyer' => ['MASTER'],
        'tipe-produk' => ['MASTER'],
        'jenis-produk' => ['MASTER'],
        'department' => ['MASTER'],
        'working-shift' => ['MASTER'],
        'warehouse' => ['MASTER'],
        'mesin' => ['MASTER'],
        'bagian-mesin' => ['MASTER'],
        'detail-bagian-mesin' => ['MASTER'],
        'jadwal-mesin' => ['MASTER'],
        'karyawan' => ['MASTER'],
        'menu-katanuki' => ['MASTER'],
        'product' => ['MASTER'],
        'add-master-product' => ['MASTER'],
        'edit-master-product' => ['MASTER'],
        'menu-loss-infure' => ['MASTER'],
        'menu-loss-katagori' => ['MASTER'],
        'menu-loss-kenpin' => ['MASTER'],
        'menu-loss-klasifikisasi' => ['MASTER'],
        'menu-loss-seitai' => ['MASTER'],
        'kemasan-box' => ['MASTER'],
        'kemasan-gasio' => ['MASTER'],
        'kemasan-inner' => ['MASTER'],
        'kemasan-layer' => ['MASTER'],
        'master-jam-mati-mesin-infure' => ['MASTER'],
        'master-jam-mati-mesin-seitai' => ['MASTER'],
        'masalah-kenpin-infure' => ['MASTER'],
        'masalah-kenpin-seitai' => ['MASTER'],

        'security-management' => ['ADM'],
        'add-user' => ['ADM'],
        'edit-user' => ['ADM'],
        'role-management' => ['ADM'],

        'pemasukan-barang' => ['ADMIN'],
        'pengeluaran-barang' => ['ADMIN'],
        'posisi-wip' => ['ADMIN'],
        'bahan-baku' => ['ADMIN'],
        'barang-jadi' => ['ADMIN'],
        'mesin-peralatan' => ['ADMIN'],
        'barang-reject' => ['ADMIN'],

        'dashboard-infure-old' => ['DASHBOARD-INFURE'],
        'dashboard-infure-kadou-jikan-infure' => ['DASHBOARD-INFURE'],
        'dashboard-infure-hasil-produksi-infure' => ['DASHBOARD-INFURE'],
        'dashboard-infure-loss-infure' => ['DASHBOARD-INFURE'],
        'dashboard-infure-top-loss-infure' => ['DASHBOARD-INFURE'],
        'dashboard-infure-counter-trouble-infure' => ['DASHBOARD-INFURE'],
        'dashboard-infure' => ['DASHBOARD-INFURE'],
        'dashboard-infure-produksi-loss-per-mesin' => ['DASHBOARD-INFURE'],
        'dashboard-infure-top-loss-per-mesin' => ['DASHBOARD-INFURE'],
        'dashboard-infure-top-loss-per-kasus' => ['DASHBOARD-INFURE'],
        'dashboard-infure-kadou-jikan-frekuensi-trouble' => ['DASHBOARD-INFURE'],
        'dashboard-infure-top-mesin-masalah-loss-daily' => ['DASHBOARD-INFURE'],
        'dashboard-infure-ranking-problem-machine-daily' => ['DASHBOARD-INFURE'],
        'dashboard-infure-total-produksi-per-bulan' => ['DASHBOARD-INFURE'],
        'dashboard-infure-peringatan-katagae' => ['DASHBOARD-INFURE'],
        'dashboard-infure-loss-per-bulan' => ['DASHBOARD-INFURE'],
        'dashboard-infure-produksi-per-bulan' => ['DASHBOARD-INFURE'],
        'dashboard-infure-top-mesin-masalah-loss-monthly' => ['DASHBOARD-INFURE'],
        'dashboard-infure-ranking-problem-machine-monthly' => ['DASHBOARD-INFURE'],
        'dashboard-seitai' => ['DASHBOARD-SEITAI'],
    ];

    /** Named routes intentionally left auth-only (no access: middleware), per the spec. */
    private const UNGATED_NAMED_ALLOWLIST = [
        'printer.settings',
    ];

    /** Unnamed routes (matched by URI) intentionally left auth-only. */
    private const UNGATED_URI_ALLOWLIST = [
        'docs/{file}',
        '/',
        'test',
        '{any}',
    ];

    public function test_every_auth_route_is_either_mapped_or_allowlisted(): void
    {
        $unexpected = [];
        $mismatched = [];

        foreach (Route::getRoutes() as $route) {
            $middlewareNames = $route->gatherMiddleware();

            if (! in_array('auth', $middlewareNames, true)) {
                continue;
            }

            $accessCodes = $this->extractAccessCodes($middlewareNames);
            $name = $route->getName();

            if ($name !== null && array_key_exists($name, self::EXPECTED)) {
                if ($accessCodes !== self::EXPECTED[$name]) {
                    $mismatched[] = sprintf(
                        '%s: expected [%s], got [%s]',
                        $name,
                        implode(',', self::EXPECTED[$name]),
                        implode(',', $accessCodes)
                    );
                }
                continue;
            }

            if ($name !== null && in_array($name, self::UNGATED_NAMED_ALLOWLIST, true)) {
                if ($accessCodes !== []) {
                    $mismatched[] = sprintf('%s: expected no access: middleware, got [%s]', $name, implode(',', $accessCodes));
                }
                continue;
            }

            if ($name === null && in_array($route->uri(), self::UNGATED_URI_ALLOWLIST, true)) {
                if ($accessCodes !== []) {
                    $mismatched[] = sprintf('%s: expected no access: middleware, got [%s]', $route->uri(), implode(',', $accessCodes));
                }
                continue;
            }

            $unexpected[] = $name ?? $route->uri();
        }

        $this->assertSame([], $unexpected, 'Routes with no mapping and no allowlist entry: ' . implode(', ', $unexpected));
        $this->assertSame([], $mismatched, 'Routes whose access: middleware does not match EXPECTED: ' . implode('; ', $mismatched));
    }

    private function extractAccessCodes(array $middlewareNames): array
    {
        foreach ($middlewareNames as $middleware) {
            if (str_starts_with($middleware, 'access:')) {
                return explode(',', substr($middleware, strlen('access:')));
            }
        }

        return [];
    }
}
