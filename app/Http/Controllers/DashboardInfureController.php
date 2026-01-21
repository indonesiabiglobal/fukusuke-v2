<?php

namespace App\Http\Controllers;

use App\Helpers\departmentHelper;
use App\Helpers\LossInfureHelper;
use App\Helpers\workingShiftHelper;
use App\Models\MsDepartment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPSTORM_META\map;

class DashboardInfureController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();
        if ($now->between(Carbon::parse($now->format('Y-m-d 00:00:00')), Carbon::parse($now->format('Y-m-d 15:00:00')))) {
            $filterDateDaily = Carbon::now()->subDay()->format('d-m-Y');
        } else {
            $filterDateDaily = Carbon::now()->format('d-m-Y');
        }

        $data = [
            'period' => ['A', 'B', 'C'],
            'listFactory' => departmentHelper::infurePabrikDepartment(),
            'filterDateDaily' => $filterDateDaily,
            'filterDateMonthly' => Carbon::now()->format('Y-m'),
        ];
        return view('dashboard.infure', $data);
    }

    public function getProduksiLossInfure(Request $request, $monthly = false)
    {
        if ($monthly) {
            $startDate = Carbon::parse($request->filterDateMonthly)->startOfMonth()->format('d-m-Y 07:01:00');
            $endDate = Carbon::parse($request->filterDateMonthly)->endOfMonth()->addDay()->format('d-m-Y 07:00:00');
        } else {
            [$startDate, $endDate] = workingShiftHelper::dailtShift($request->filterDateDaily, Carbon::parse($request->filterDateDaily)->addDay()->format('d-m-Y'));
        }
        $lossClassIds = LossInfureHelper::lossClassIdDashboard();
        $placeholders = implode(',', array_fill(0, count($lossClassIds), '?'));

        $sql = "
            SELECT
                mac.id AS machine_id,
                RIGHT(mac.machineno, 2) AS machineno,
                ROUND(COALESCE(SUM(tpa.berat_produksi), 0)::numeric, 1) AS berat_produksi,
                ROUND(COALESCE(SUM(tpaloss.berat_loss), 0)::numeric, 1) AS berat_loss
            FROM msmachine mac
            LEFT JOIN tdproduct_assembly tpa
                ON mac.id = tpa.machine_id
                AND tpa.production_date BETWEEN ? AND ?
            LEFT JOIN tdproduct_assembly_loss tpaloss
                ON tpa.id = tpaloss.product_assembly_id
                AND EXISTS (
                    SELECT 1
                    FROM mslossinfure msl
                    WHERE msl.id = tpaloss.loss_infure_id
                    AND msl.loss_class_id IN ($placeholders)
                )
            WHERE mac.department_id = ?
                AND mac.status = 1
            GROUP BY mac.id, mac.machineno
            ORDER BY machineno ASC
        ";

        // Gabungkan semua parameter
        $params = array_merge([$startDate, $endDate], $lossClassIds, [$request->factory]);

        $produksiLossDaily = collect(DB::select($sql, $params))->map(function ($item) {
            $item->berat_loss_percentage = $item->berat_produksi > 0
                ? round(($item->berat_loss / $item->berat_produksi) * 100, 2)
                : 0;
            return $item;
        });

        return $produksiLossDaily;
    }

    public function getTopLossByMachineInfure(Request $request)
    {
        [$startDate, $endDate] = workingShiftHelper::dailtShift($request->filterDateDaily, Carbon::parse($request->filterDateDaily)->addDay()->format('d-m-Y'));
        $lossClassIds = LossInfureHelper::lossClassIdDashboard();

        $topLossInfure = DB::select('
            SELECT
                mac.id AS machine_id,
                RIGHT(mac.machineno, 2) AS machineno,
                ROUND(COALESCE(SUM(tpaloss.berat_loss), 0)::numeric, 1) as berat_loss
            FROM msmachine mac
            LEFT JOIN tdproduct_assembly tpa
                ON mac.id = tpa.machine_id
                AND tpa.production_date between ? AND ?
            LEFT JOIN tdproduct_assembly_loss as tpaloss
                ON tpa.id = tpaloss.product_assembly_id
                AND EXISTS (
                    SELECT 1
                    FROM mslossinfure msl
                    WHERE msl.id = tpaloss.loss_infure_id
                    AND msl.loss_class_id IN (' . implode(',', $lossClassIds ) . ')
                )
            WHERE mac.department_id = ?
                AND mac.status = 1
            GROUP BY mac.id, mac.machineno
            ORDER BY berat_loss DESC limit 5
        ', [
            $startDate,
            $endDate,
            $request->factory,
        ]);

        return $topLossInfure;
    }

    public function getTopLossByKasusInfure(Request $request)
    {
        [$startDate, $endDate] = workingShiftHelper::dailtShift($request->filterDateDaily, Carbon::parse($request->filterDateDaily)->addDay()->format('d-m-Y'));
        $lossClassIds = LossInfureHelper::lossClassIdDashboard();

        $topLossKasus = DB::select('
            SELECT
                mslos.name as loss_name,
                ROUND(SUM(tpaloss.berat_loss)::numeric, 1) as berat_loss
            FROM mslossinfure mslos
            INNER JOIN tdproduct_assembly_loss as tpaloss on mslos.id = tpaloss.loss_infure_id
            INNER JOIN tdproduct_assembly tpa
            ON tpaloss.product_assembly_id = tpa.id
                AND tpa.production_date between ? AND ?
            INNER JOIN msmachine mac ON tpa.machine_id = mac.id
            WHERE mac.department_id = ?
                AND mslos.loss_class_id IN (' . implode(',', $lossClassIds ) . ')
                AND mac.status = 1
            GROUP BY loss_name
            ORDER BY berat_loss DESC limit 5
        ', [
            $startDate,
            $endDate,
            $request->factory,
        ]);

        return $topLossKasus;
    }

    public function getTopMesinMasalahLossDaily(Request $request)
    {
        [$startDate, $endDate] = workingShiftHelper::dailtShift($request->filterDateDaily, Carbon::parse($request->filterDateDaily)->addDay()->format('d-m-Y'));

        $topLossKasus = DB::select('
            SELECT
                mac.id AS machine_id,
                top_loss.loss_name,
                RIGHT(mac.machineno, 2) AS machineno,
                ROUND(COALESCE(SUM(tpaloss.berat_loss), 0)::numeric, 1) as berat_loss
            FROM msmachine mac
            INNER JOIN tdproduct_assembly tpa ON mac.id = tpa.machine_id
            INNER JOIN tdproduct_assembly_loss tpaloss ON tpa.id = tpaloss.product_assembly_id
            INNER JOIN (
                SELECT
                    tpaloss.loss_infure_id,
                    mslos.name as loss_name,
                    ROUND(SUM(tpaloss.berat_loss)::numeric, 1) as total_loss
                FROM mslossinfure mslos
                INNER JOIN tdproduct_assembly_loss tpaloss ON mslos.id = tpaloss.loss_infure_id
                INNER JOIN tdproduct_assembly tpa ON tpaloss.product_assembly_id = tpa.id
                INNER JOIN msmachine mac ON tpa.machine_id = mac.id
                WHERE mac.department_id = ?
                    AND mac.status = 1
                    AND tpa.production_date BETWEEN ? AND ?
                    AND mslos.loss_class_id IN (' . implode(',', LossInfureHelper::lossClassIdDashboard()) . ')
                GROUP BY tpaloss.loss_infure_id, mslos.name
                ORDER BY total_loss DESC
                LIMIT 1
            ) AS top_loss
                ON tpaloss.loss_infure_id = top_loss.loss_infure_id
            WHERE mac.department_id = ?
                AND tpa.production_date BETWEEN ? AND ?
                AND mac.status = 1
            GROUP BY mac.id, mac.machineno, top_loss.loss_name
            ORDER BY berat_loss DESC
            LIMIT 3
        ', [
            $request->factory,
            $startDate,
            $endDate,
            $request->factory,
            $startDate,
            $endDate
        ]);

        return $topLossKasus;
    }

    public function getKadouJikanFrekuensiTrouble(Request $request, $monthly = false)
    {
        if ($monthly) {
            $startDate = Carbon::parse($request->filterDateMonthly)->startOfMonth()->format('d-m-Y 07:01:00');
            $endDate = Carbon::parse($request->filterDateMonthly)->endOfMonth()->addDay()->format('d-m-Y 07:00:00');
        } else {
            // Daily shift calculation
            [$startDate, $endDate] = workingShiftHelper::dailtShift($request->filterDateDaily, Carbon::parse($request->filterDateDaily)->addDay()->format('d-m-Y'));
        }

        $query = "
            SELECT
                mac.id AS machine_id,
                RIGHT(mac.machineno, 2) AS machine_no,
                COALESCE(jam.work_hour_mm, 0) AS work_hour_mm,
                COALESCE(jam.work_hour_on_mm, 0) AS work_hour_on_mm,
                CASE
                    WHEN COALESCE(jam.work_hour_mm, 0) = 0 THEN 0
                    ELSE ROUND(COALESCE(jam.work_hour_on_mm, 0) / COALESCE(jam.work_hour_mm, 0) * 100, 2)
                END as kadou_jikan,
                COALESCE(jam.frekuensi_trouble, 0) AS frekuensi_trouble
            FROM msmachine mac
            LEFT JOIN (
                SELECT
                    machine_id,
                    SUM(EXTRACT(hour FROM work_hour) * 60 + EXTRACT(minute FROM work_hour)) AS work_hour_mm,
                    SUM(EXTRACT(hour FROM on_hour) * 60 + EXTRACT(minute FROM on_hour)) AS work_hour_on_mm,
                    COUNT(tjkjmm.id) AS frekuensi_trouble
                FROM tdjamkerjamesin
                LEFT JOIN tdjamkerja_jammatimesin tjkjmm ON tjkjmm.jam_kerja_mesin_id = tdjamkerjamesin.id
                WHERE working_date BETWEEN ? AND ?
                GROUP BY machine_id
            ) jam ON mac.id = jam.machine_id
            WHERE mac.status = 1
                AND mac.department_id = ?
            ORDER BY RIGHT(mac.machineno, 2)
        ";

        $kadouJikanInfureMesin = DB::select($query, [
            $startDate,
            $endDate,
            $request->factory
        ]);

        return $kadouJikanInfureMesin;
    }

    public function getRankingProblemMachineDaily(Request $request)
    {
        $produksiLoss = $this->getProduksiLossInfure($request);
        $kadouJikan = $this->getKadouJikanFrekuensiTrouble($request);

        $rankingProduksi = collect($produksiLoss)->map(function ($item) {
            return [
                'machine_id' => $item->machine_id,
                'machineno' => $item->machineno,
                'berat_produksi' => $item->berat_produksi,
            ];
        })->sortBy('berat_produksi')
            ->values()
            ->map(function ($item, $index) {
                return [
                    'rank' => $index + 1,
                    ...$item
                ];
            });

        $rankingLoss = collect($produksiLoss)->map(function ($item) {
            return [
                'machine_id' => $item->machine_id,
                'machineno' => $item->machineno,
                'berat_loss' => $item->berat_loss,
                'berat_loss_percentage' => $item->berat_loss_percentage,
            ];
        })->sortByDesc('berat_loss')
            ->values()
            ->map(function ($item, $index) {
                return [
                    'rank' => $index + 1,
                    ...$item
                ];
            });

        $rankingKadouJikan = collect($kadouJikan)->map(function ($item) {
            return [
                'machine_id' => $item->machine_id,
                'machineno' => $item->machine_no,
                'kadou_jikan' => $item->kadou_jikan,
                'frekuensi_trouble' => $item->frekuensi_trouble,
            ];
        })->sortBy('kadou_jikan')->values()->map(function ($item, $index) {
            return [
                'rank' => $index + 1,
                ...$item
            ];
        })->toArray();

        $rankingAll = collect();
        foreach ($rankingKadouJikan as $key => $item) {
            $produksi = $rankingProduksi->firstWhere('machine_id', $item['machine_id']);
            $loss = $rankingLoss->firstWhere('machine_id', $item['machine_id']);

            if ($produksi['berat_produksi'] == 0 && $loss['berat_loss'] == 0 && $item['kadou_jikan'] == 0) {
                continue; // skip if no production and no loss and no kadou jikan
            }
            $rankingAll[] = [
                'sum_rank' => $produksi['rank'] + $loss['rank'] + $item['rank'],
                'machine_id' => $item['machine_id'],
                'machineno' => $item['machineno'],
            ];
        }
        $rankingAll = $rankingAll->sortBy('sum_rank')->take(3)->values();

        return $rankingAll;
    }

    /*
    Monthly Dashboard
    */
    // get produksi per bulan
    public function getTotalProductionMonthly(Request $request)
    {
        $filterDate = Carbon::parse($request->filterDateMonthly);
        $startMonth = Carbon::parse($filterDate)->startOfMonth()->format('d-m-Y 07:01:00');
        $firstPeriod = Carbon::parse($startMonth)->addDays(10)->format('d-m-Y 07:00:00');
        $secondPeriod = Carbon::parse($firstPeriod)->addDays(10)->format('d-m-Y 07:00:00');
        $endMonth = Carbon::parse($filterDate)->endOfMonth()->addDay()->format('d-m-Y 07:00:00');

        $totalProductionMonthly = collect(DB::select('
            SELECT
                ROUND(COALESCE(SUM(tpa.berat_produksi), 0)::numeric, 1) as target_produksi,
                ROUND(COALESCE(SUM(tpa.berat_produksi), 0)::numeric, 1) as total_produksi,
                CASE
                    WHEN tpa.production_date BETWEEN :startMonth AND :firstPeriod THEN 1
                    WHEN tpa.production_date BETWEEN :firstPeriodPlus AND :secondPeriod THEN 2
                    WHEN tpa.production_date BETWEEN :secondPeriodPlus AND :endMonth THEN 3
                END AS period_ke
            FROM tdproduct_assembly tpa
            LEFT JOIN msmachine mac ON tpa.machine_id = mac.id
            WHERE mac.department_id = :factory
                AND tpa.production_date BETWEEN :startMonth AND :endMonth
                AND mac.status = 1
            GROUP BY period_ke
            ORDER BY period_ke ASC
        ', [
            'factory'         => $request->factory,
            'startMonth'      => $startMonth,
            'firstPeriod'     => $firstPeriod,
            'firstPeriodPlus' => Carbon::parse($firstPeriod)->format('Y-m-d 07:00:00'),
            'secondPeriod'    => $secondPeriod,
            'secondPeriodPlus' => Carbon::parse($secondPeriod)->format('Y-m-d 07:00:00'),
            'endMonth'        => $endMonth,
        ]));

        return $totalProductionMonthly;
    }

    public function getPeringatanKatagae(Request $request)
    {
        $filterDate = Carbon::parse($request->filterDateMonthly);
        $startMonth = Carbon::parse($filterDate)->startOfMonth()->format('d-m-Y 07:01:00');
        $endMonth = Carbon::parse($filterDate)->endOfMonth()->addDay()->format('d-m-Y 07:00:00');

        $peringatanKatagaeMonthly = collect(DB::select('
            SELECT
                mac.id AS machine_id,
                mac.capacity_kg,
                RIGHT(mac.machineno, 2) AS machineno,
                tolpk.lpk_no,
                msp.name AS product_name,
                msp.unit_weight,
                msp.productlength,
                MAX(tolpk.panjang_lpk - tolpk.total_assembly_line) AS sisa_meter
            FROM msmachine mac
            INNER JOIN tdorderlpk tolpk ON mac.id = tolpk.machine_id
            INNER JOIN tdorder tol ON tolpk.order_id = tol.id
            INNER JOIN msproduct msp ON tol.product_id = msp.id
            WHERE mac.department_id = :factory
                AND tolpk.lpk_date BETWEEN :startMonth AND :endMonth
                AND (tolpk.panjang_lpk - tolpk.total_assembly_line) > 0
                AND mac.status = 1
            GROUP BY mac.id, mac.machineno, tolpk.lpk_no, msp.name, msp.unit_weight, msp.productlength
            ORDER BY sisa_meter DESC
            LIMIT 5
        ', [
            'factory' => $request->factory,
            'startMonth' => $startMonth,
            'endMonth' => $endMonth,
        ]))->map(function ($item) {
            $berat = $item->unit_weight / $item->productlength * $item->sisa_meter;
            $menit = $berat/$item->capacity_kg*60;
            return [
                'machine_id' => $item->machine_id,
                'machineno' => $item->machineno,
                'lpk_no' => $item->lpk_no,
                'product_name' => $item->product_name,
                'unit_weight' => $item->unit_weight,
                'productlength' => $item->productlength,
                'sisa_meter' => $item->sisa_meter,
                'berat' => $berat,
                'jam' => floor($menit / 60),
                'menit' => $menit % 60,
            ];
        });

        return $peringatanKatagaeMonthly;
    }

    // get loss per bulan
    public function getLossMonthly(Request $request)
    {
        $filterDate = Carbon::parse($request->filterDateMonthly);
        $startMonth = Carbon::parse($filterDate)->startOfMonth()->format('d-m-Y 07:01:00');
        $firstPeriod = Carbon::parse($startMonth)->addDays(10)->format('d-m-Y 07:00:00');
        $secondPeriod = Carbon::parse($firstPeriod)->addDays(10)->format('d-m-Y 07:00:00');
        $endMonth = Carbon::parse($filterDate)->endOfMonth()->addDay()->format('d-m-Y 07:00:00');

        $produksiLossMonthly = collect(DB::select('
            SELECT
                mac.id AS machine_id,
                RIGHT(mac.machineno, 2) AS machineno,
                CASE
                    WHEN tpa.production_date BETWEEN :startMonth AND :firstPeriod THEN 1
                    WHEN tpa.production_date BETWEEN :firstPeriodPlus AND :secondPeriod THEN 2
                    WHEN tpa.production_date BETWEEN :secondPeriodPlus AND :endMonth THEN 3
                END AS period_ke,
                ROUND(COALESCE(SUM(tpa.berat_produksi), 0)::numeric, 1) as berat_produksi,
                ROUND(SUM(tpaloss.berat_loss)::numeric, 1) AS berat_loss
            FROM msmachine mac
            LEFT JOIN tdproduct_assembly tpa ON mac.id = tpa.machine_id
            LEFT JOIN tdproduct_assembly_loss tpaloss ON tpa.id = tpaloss.product_assembly_id
            WHERE mac.department_id = :factory
                AND tpa.production_date BETWEEN :startMonth AND :endMonth
                AND mac.status = 1
            GROUP BY mac.id, mac.machineno, period_ke
            ORDER BY mac.machineno ASC, period_ke ASC
        ', [
            'factory'         => $request->factory,
            'startMonth'      => $startMonth,
            'firstPeriod'     => $firstPeriod,
            'firstPeriodPlus' => Carbon::parse($firstPeriod)->format('Y-m-d 07:00:00'),
            'secondPeriod'    => $secondPeriod,
            'secondPeriodPlus' => Carbon::parse($secondPeriod)->format('Y-m-d 07:00:00'),
            'endMonth'        => $endMonth,
        ]))->groupBy('period_ke')->map(function ($items, $period) {
            return $items->map(function ($item) use ($period) {
                return [
                    'machine_id' => $item->machine_id,
                    'machineno' => $item->machineno,
                    'berat_loss' => $item->berat_produksi > 0
                        ? round(($item->berat_loss / $item->berat_produksi) * 100, 2)
                        : 0,
                    'period_ke' => $period
                ];
            });
        })->toArray();

        return $produksiLossMonthly;
    }

    // get produksi per bulan
    public function getProductionMonthly(Request $request)
    {
        $filterDate = Carbon::parse($request->filterDateMonthly);
        $startMonth = Carbon::parse($filterDate)->startOfMonth()->format('d-m-Y 07:01:00');
        $firstPeriod = Carbon::parse($startMonth)->addDays(10)->format('d-m-Y 07:00:00');
        $secondPeriod = Carbon::parse($firstPeriod)->addDays(10)->format('d-m-Y 07:00:00');
        $endMonth = Carbon::parse($filterDate)->endOfMonth()->addDay()->format('d-m-Y 07:00:00');

        $produksiLossMonthly = collect(DB::select('
            SELECT
                mac.id AS machine_id,
                RIGHT(mac.machineno, 2) AS machineno,
                CASE
                    WHEN tpa.production_date BETWEEN :startMonth AND :firstPeriod THEN 1
                    WHEN tpa.production_date BETWEEN :firstPeriodPlus AND :secondPeriod THEN 2
                    WHEN tpa.production_date BETWEEN :secondPeriodPlus AND :endMonth THEN 3
                END AS period_ke,
                ROUND(SUM(tpa.berat_produksi)::numeric, 1) AS berat_produksi
            FROM msmachine mac
            LEFT JOIN tdproduct_assembly tpa ON mac.id = tpa.machine_id
            WHERE mac.department_id = :factory
                AND tpa.production_date BETWEEN :startMonth AND :endMonth
                AND mac.status = 1
            GROUP BY mac.id, mac.machineno, period_ke
            ORDER BY mac.id ASC, period_ke ASC
        ', [
            'factory'         => $request->factory,
            'startMonth'      => $startMonth,
            'firstPeriod'     => $firstPeriod,
            'firstPeriodPlus' => Carbon::parse($firstPeriod)->format('Y-m-d 07:00:00'),
            'secondPeriod'    => $secondPeriod,
            'secondPeriodPlus' => Carbon::parse($secondPeriod)->format('Y-m-d 07:00:00'),
            'endMonth'        => $endMonth,
        ]))->groupBy('period_ke')->map(function ($items, $period) {
            return $items->map(function ($item) use ($period) {
                return [
                    'machine_id' => $item->machine_id,
                    'machineno' => $item->machineno,
                    'berat_produksi' => $item->berat_produksi,
                    'period_ke' => $period
                ];
            });
        })->toArray();

        return $produksiLossMonthly;
    }

    public function getRankingProblemMachineMonthly(Request $request)
    {
        $produksiLoss = $this->getProduksiLossInfure($request, true);
        $kadouJikan = $this->getKadouJikanFrekuensiTrouble($request, true);

        $rankingProduksi = collect($produksiLoss)->map(function ($item) {
            return [
                'machine_id' => $item->machine_id,
                'machineno' => $item->machineno,
                'berat_produksi' => $item->berat_produksi,
            ];
        })->sortBy('berat_produksi')
            ->values()
            ->map(function ($item, $index) {
                return [
                    'rank' => $index + 1,
                    ...$item
                ];
            });

        $rankingLoss = collect($produksiLoss)->map(function ($item) {
            return [
                'machine_id' => $item->machine_id,
                'machineno' => $item->machineno,
                'berat_loss' => $item->berat_loss,
                'berat_loss_percentage' => $item->berat_loss_percentage,
            ];
        })->sortByDesc('berat_loss')
            ->values()
            ->map(function ($item, $index) {
                return [
                    'rank' => $index + 1,
                    ...$item
                ];
            });

        $rankingKadouJikan = collect($kadouJikan)->map(function ($item) {
            return [
                'machine_id' => $item->machine_id,
                'machineno' => $item->machine_no,
                'kadou_jikan' => $item->kadou_jikan,
                'frekuensi_trouble' => $item->frekuensi_trouble,
            ];
        })->sortBy('kadou_jikan')->values()->map(function ($item, $index) {
            return [
                'rank' => $index + 1,
                ...$item
            ];
        })->toArray();

        $rankingAll = collect();
        foreach ($rankingKadouJikan as $key => $item) {
            $produksi = $rankingProduksi->firstWhere('machine_id', $item['machine_id']);
            $loss = $rankingLoss->firstWhere('machine_id', $item['machine_id']);

            if ($produksi['berat_produksi'] == 0 && $loss['berat_loss'] == 0 && $item['kadou_jikan'] == 0) {
                continue; // skip if no production and no loss and no kadou jikan
            }
            $rankingAll[] = [
                'sum_rank' => $produksi['rank'] + $loss['rank'] + $item['rank'],
                'produksi_rank' => $produksi['rank'],
                'loss_rank' => $loss['rank'],
                'kadou_jikan_rank' => $item['rank'],
                'machine_id' => $item['machine_id'],
                'machineno' => $item['machineno'],
            ];
        }
        $rankingAll = $rankingAll->sortBy('sum_rank')->take(3)->values();

        return $rankingAll;
    }

    public function getTopMesinMasalahLossMonthly(Request $request)
    {
        $filterDate = Carbon::parse($request->filterDateMonthly);
        $startMonth = Carbon::parse($filterDate)->startOfMonth()->format('d-m-Y 07:01:00');
        $endMonth = Carbon::parse($filterDate)->endOfMonth()->addDay()->format('d-m-Y 07:00:00');

        $topLossKasus = DB::select('
            SELECT
                mac.id AS machine_id,
                top_loss.loss_name,
                RIGHT(mac.machineno, 2) AS machineno,
                ROUND(COALESCE(SUM(tpaloss.berat_loss), 0)::numeric, 1) as berat_loss
            FROM msmachine mac
            INNER JOIN tdproduct_assembly tpa ON mac.id = tpa.machine_id
            INNER JOIN tdproduct_assembly_loss tpaloss ON tpa.id = tpaloss.product_assembly_id
            INNER JOIN (
                SELECT
                    tpaloss.loss_infure_id,
                    mslos.name as loss_name,
                    ROUND(SUM(tpaloss.berat_loss)::numeric, 1) as total_loss
                FROM mslossinfure mslos
                INNER JOIN tdproduct_assembly_loss tpaloss ON mslos.id = tpaloss.loss_infure_id
                INNER JOIN tdproduct_assembly tpa ON tpaloss.product_assembly_id = tpa.id
                INNER JOIN msmachine mac ON tpa.machine_id = mac.id
                WHERE mac.department_id = ?
                    AND tpa.production_date BETWEEN ? AND ?
                    AND mslos.loss_class_id IN (' . implode(',', LossInfureHelper::lossClassIdDashboard()) . ')
                GROUP BY tpaloss.loss_infure_id, mslos.name
                ORDER BY total_loss DESC
                LIMIT 1
            ) AS top_loss
                ON tpaloss.loss_infure_id = top_loss.loss_infure_id
            WHERE mac.department_id = ?
                AND tpa.production_date BETWEEN ? AND ?
                AND mac.status = 1
            GROUP BY mac.id, mac.machineno, top_loss.loss_name
            ORDER BY berat_loss DESC
            LIMIT 3
        ', [
            $request->factory,
            $startMonth,
            $endMonth,
            $request->factory,
            $startMonth,
            $endMonth
        ]);

        return $topLossKasus;
    }
}
