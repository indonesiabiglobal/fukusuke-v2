<?php

namespace App\Http\Controllers;

use App\Models\MsDepartment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPSTORM_META\map;

class DashboardInfureController extends Controller
{
    public function index(Request $request)
    {
        if (isset($request->filterDate)) {
            $requestFilterDate = $request->filterDate;
            $filterDate = explode(' to ', $request->filterDate);
            $startDate = Carbon::parse($filterDate[0])->format('d-m-Y 00:00:00');
            if (count($filterDate) == 1) {
                $endDate = Carbon::parse($filterDate[0])->format('d-m-Y 23:59:59');
            } else {
                $endDate = Carbon::parse($filterDate[1])->format('d-m-Y 23:59:59');
            }
        } else {
            $startDate = Carbon::now()->startOfMonth()->format('d-m-Y 00:00:00');
            $endDate = Carbon::now()->format('d-m-Y 23:59:59');
            $requestFilterDate = $startDate . ' to ' . $endDate;
        }
        $divisionCodeInfure = MsDepartment::where('name', 'INFURE')->first()->division_code;

        // LOSS INFURE
        $lossInfure = $this->getLossInfure($startDate, $endDate, $divisionCodeInfure);
        $topLossInfure = $this->getTopLossInfure($startDate, $endDate, $divisionCodeInfure);

        if ($topLossInfure != null) {
            $higherLoss = $topLossInfure[0]->berat_loss;
        } else {
            $higherLoss = 0;
        }

        if ($lossInfure['totalLossInfure'] != 0) {
            $higherLossPercentage = round(($higherLoss / $lossInfure['totalLossInfure']) * 100, 2);
        } else {
            $higherLossPercentage = 0;
        }

        $higherLossName = $topLossInfure[0]->loss_name;

        $listMachineInfure = $this->getListMachineInfure($startDate, $endDate, $divisionCodeInfure);
        $kadouJikan = $this->getKadouJikanInfure($startDate, $endDate, $divisionCodeInfure);
        $kadouJikanDepartment = array_reduce($listMachineInfure['listDepartment'], function ($carry, $item) use ($kadouJikan) {
            $totalPersenMesin = array_reduce($kadouJikan, function ($carry, $itemKadou) use ($item) {
                if ($itemKadou->department_id == $item['department_id']) {
                    $carry += $itemKadou->persenmesinkerja;
                }
                return $carry;
            }, 0);

            $countMesin = array_reduce($kadouJikan, function ($carry, $itemKadou) use ($item) {
                if ($itemKadou->department_id == $item['department_id']) {
                    $carry += 1;
                }
                return $carry;
            }, 0);
            $carry[$item['department_id']] = [
                'departmentId' => $item['department_id'],
                'departmentName' => $item['department_name'],
                'persenMesinDepartment' => $totalPersenMesin / $countMesin
            ];
            return $carry;
        }, []);

        $data = [
            'filterDate' => $requestFilterDate,

            // Infure
            'listMachineInfure' => $listMachineInfure,
            'kadouJikanInfureMesin' => $kadouJikan,
            'kadouJikanDepartment' => $kadouJikanDepartment,
            'hasilProduksiInfure' => $this->getHasilProduksiInfure($startDate, $endDate),
            'counterTroubleInfure' => $this->getCounterTroubleInfure($startDate, $endDate),
            'lossInfure' => $lossInfure,
            'topLossInfure' => $topLossInfure,
            'higherLoss' => round($higherLoss, 2),
            'higherLossPercentage' => $higherLossPercentage,
            'higherLossName' => $higherLossName
        ];
        return view('dashboard.infure', $data);
    }

    /*
    Infure
    */
    public function getListMachineInfure($startDate, $endDate, $divisionCodeInfure)
    {
        $listMachineInfure = DB::select('
        SELECT
            RIGHT( mac.machineno, 2 ) AS machineno,
            dep."id" as department_id,
            dep.division_code,
            dep."name" as department_name
        FROM
            "msmachine" AS mac
            INNER JOIN msdepartment AS dep ON mac.department_id = dep.ID
        WHERE
            mac.status = 1 AND division_code = ?
        ORDER BY machineno ASC

        ', [$divisionCodeInfure]);
        // ', array_merge([$startDate, $endDate, $division_code], $machineNo));
        $listDepartment = array_reduce($listMachineInfure, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        return [
            'listMachineInfure' => $listMachineInfure,
            'listDepartment' => $listDepartment
        ];
    }
    public function getKadouJikanInfure($startDate, $endDate, $divisionCodeInfure)
    {
        $divisionCodeInfure = MsDepartment::where('name', 'INFURE')->first()->division_code;
        $diffDay = Carbon::parse($endDate)->diffInDays(Carbon::parse($startDate)) + 1;
        $minuteOfDay = 24 * 60;

        $kadouJikanInfureMesin = DB::select('
        SELECT y.* FROM (
            SELECT x.*,round(x.work_hour_on_mm/?*100,2) as persenmesinkerja from (
                SELECT RIGHT(mac.machineno, 2) AS machine_no,
                    mac.machineno AS machineno,
                    mac.machinename AS machine_name,
                    dep.name AS department_name,
                    dep.id AS department_id,
                    dep.division_code,
                    COALESCE(jam.work_hour_mm, 0) AS work_hour_mm,
                    COALESCE(jam.work_hour_off_mm, 0) AS work_hour_off_mm,
                    COALESCE(jam.work_hour_on_mm, 0) AS work_hour_on_mm
                FROM
                    msmachine AS mac
                INNER JOIN
                    msdepartment AS dep ON mac.department_id = dep.id
                LEFT JOIN (
                    SELECT
                        jam_.machine_id,
                        SUM(EXTRACT(hour FROM jam_.work_hour) * 60 + EXTRACT(minute FROM jam_.work_hour)) AS work_hour_mm,
                        SUM(EXTRACT(hour FROM jam_.off_hour) * 60 + EXTRACT(minute FROM jam_.off_hour)) AS work_hour_off_mm,
                        SUM(EXTRACT(hour FROM jam_.on_hour) * 60 + EXTRACT(minute FROM jam_.on_hour)) AS work_hour_on_mm
                    FROM
                        tdjamkerjamesin AS jam_
                    WHERE jam_.working_date BETWEEN
                         ? AND ?
                    GROUP BY
                        jam_.machine_id
                ) AS jam ON mac.id = jam.machine_id
            WHERE
                mac.status = 1
                    and dep.division_code= ?
            ) as x ) as y
                -- WHERE y.persenmesinkerja > 0
            ORDER BY
                y.machine_no

        ', [$diffDay * $minuteOfDay,$startDate, $endDate, $divisionCodeInfure]);
        // ', array_merge([$startDate, $endDate, $division_code], $machineNo));
        // dd($kadouJikanInfureMesin);
        return $kadouJikanInfureMesin;
    }

    public function getHasilProduksiInfure($startDate, $endDate)
    {
        $hasilProduksiMesin = DB::select('
            SELECT x.machine_no,x.machine_name,x.department_name,
            max(x.totalpanjangproduksi) as max,min(x.totalpanjangproduksi) as min from (
                SELECT pa.created_on, right(mac.machineno, 2) as machine_no,
                    mac.machineno as machine_name,
                    dep.name as department_name,
                        sum(pa.panjang_produksi) as totalpanjangproduksi
                from tdproduct_assembly as pa
                left join msmachine as mac on mac.id=pa.machine_id
                left join msdepartment as dep on mac.department_id = dep.id
                where pa.created_on between ? and ?
                GROUP BY pa.created_on, right(mac.machineno, 2),
                    mac.machineno,
                    dep.name
                ) as x
            GROUP BY x.machine_no,x.machine_name,x.department_name
            ORDER BY x.machine_no
        ', [$startDate, $endDate]);
        // ', array_merge([$startDate, $endDate], $machineNo));

        return $hasilProduksiMesin;
    }

    public function getLossInfure($startDate, $endDate, $divisionCodeInfure)
    {
        $lossInfureMesin = DB::select('
            SELECT x.* from (
                select
                    ? as division_code,
                    max(mslos.code) as loss_code,
                    max(mslos.name) as loss_name,
                    sum(det.berat_loss) as berat_loss
                from tdproduct_assembly as hdr
                inner join tdproduct_assembly_loss as det on hdr.id = det.product_assembly_id
                inner join mslossinfure as mslos on det.loss_infure_id = mslos.id
                where hdr.created_on between ? and ?
                group by det.loss_infure_id
            ) as x order BY x.berat_loss DESC
        ', [
            $divisionCodeInfure,
            $startDate,
            $endDate,
        ]);

        // menghitung berat loss dari loss infure
        $totalLossInfure = array_sum(array_map(function ($item) {
            return $item->berat_loss;
        }, $lossInfureMesin));

        return [
            'lossInfure' => $lossInfureMesin,
            'totalLossInfure' => $totalLossInfure
        ];
    }

    public function getTopLossInfure($startDate, $endDate, $divisionCodeInfure)
    {
        $topLossInfure = DB::select('
            SELECT x.* from (
                select
                    ? as division_code,
                    max(mslos.code) as loss_code,
                    max(mslos.name) as loss_name,
                    sum(det.berat_loss) as berat_loss
                from tdproduct_assembly as hdr
                inner join tdproduct_assembly_loss as det on hdr.id = det.product_assembly_id
                inner join mslossinfure as mslos on det.loss_infure_id = mslos.id
                where hdr.created_on between ? and ?
                group by det.loss_infure_id
                ) as x order BY x.berat_loss DESC limit 3
        ', [
            $divisionCodeInfure,
            $startDate,
            $endDate,
        ]);

        return $topLossInfure;
    }

    public function getCounterTroubleInfure($startDate, $endDate)
    {
        $counterTroubleInfure = DB::select('
            SELECT x.* from (
                select
                mslos.code as loss_code,
                mslos.name as loss_name,
                    count(mslos.code) as counterloss
                from tdproduct_assembly as hdr
                inner join tdproduct_assembly_loss as det on hdr.id = det.product_assembly_id
                inner join mslossinfure as mslos on det.loss_infure_id = mslos.id
                where hdr.created_on between ? and ?
                group by mslos.code,mslos.name
            ) as x
            order BY x.loss_name ASC
        ', [
            $startDate,
            $endDate,
        ]);

        return $counterTroubleInfure;
    }
}
