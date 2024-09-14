<?php

namespace App\Http\Controllers;

use App\Models\MsDepartment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPSTORM_META\map;

class DashboardSeitaiController extends Controller
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
        $divisionCodeSeitai = MsDepartment::where('name', 'SEITAI')->first()->division_code;

        // LOSS SEITAI
        $lossSeitai = $this->getLossSeitai($startDate, $endDate, $divisionCodeSeitai);
        $topLossSeitai = $this->getTopLossSeitai($startDate, $endDate, $divisionCodeSeitai);

        if ($topLossSeitai != null) {
            $higherLoss = $topLossSeitai[0]->berat_loss;
        } else {
            $higherLoss = 0;
        }

        if ($lossSeitai['totalLossSeitai'] != 0) {
            $higherLossPercentage = round(($higherLoss / $lossSeitai['totalLossSeitai']) * 100, 2);
        } else {
            $higherLossPercentage = 0;
        }

        $higherLossName = $topLossSeitai[0]->loss_name;

        $listMachineSeitai = $this->getListMachineSeitai($startDate, $endDate, $divisionCodeSeitai);
        $kadouJikan = $this->getkadouJikanSeitai($startDate, $endDate, $divisionCodeSeitai);
        $kadouJikanDepartment = array_reduce($listMachineSeitai['listDepartment'], function ($carry, $item) use ($kadouJikan) {
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

            // Seitai
            'listMachineSeitai' => $listMachineSeitai,
            'kadouJikanSeitaiMesin' => $kadouJikan,
            'kadouJikanDepartment' => $kadouJikanDepartment,
            'hasilProduksiSeitai' => $this->getHasilProduksiSeitai($startDate, $endDate),
            'counterTroubleSeitai' => $this->getCounterTroubleSeitai($startDate, $endDate),
            'lossSeitai' => $lossSeitai,
            'topLossSeitai' => $topLossSeitai,
            'higherLoss' => round($higherLoss, 2),
            'higherLossPercentage' => $higherLossPercentage,
            'higherLossName' => $higherLossName,
        ];
        return view('dashboard.seitai', $data);
    }

    /*
    SEITAI
    */
    public function getListMachineSeitai($startDate, $endDate, $divisionCodeSeitai)
    {

        $listMachineSeitai = DB::select('
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

        ', [$divisionCodeSeitai]);
        // ', array_merge([$startDate, $endDate, $division_code], $machineNo));
        $listDepartment = array_reduce($listMachineSeitai, function ($carry, $item) {
            $carry[$item->department_id] = [
                'department_id' => $item->department_id,
                'department_name' => $item->department_name
            ];
            return $carry;
        }, []);

        return [
            'listMachineSeitai' => $listMachineSeitai,
            'listDepartment' => $listDepartment
        ];
    }
    public function getkadouJikanSeitai($startDate, $endDate, $divisionCodeSeitai)
    {
        $diffDay = Carbon::parse($endDate)->diffInDays(Carbon::parse($startDate)) + 1;
        $minuteOfDay = 24 * 60;

        $kadouJikanSeitaiMesin = DB::select('
        SELECT y.* FROM (
            SELECT x.*, ROUND(x.work_hour_on_mm / ? * 100, 2) AS persenmesinkerja
            FROM (
                SELECT
                    RIGHT(mac.machineno, 2) AS machine_no,
                    mac.machineno AS machineno,
                    mac.machinename AS machine_name,
                    dep.id AS department_id,
                    dep.name AS department_name,
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
                        SUM(EXTRACT(HOUR FROM jam_.work_hour) * 60 + EXTRACT(MINUTE FROM jam_.work_hour)) AS work_hour_mm,
                        SUM(EXTRACT(HOUR FROM jam_.off_hour) * 60 + EXTRACT(MINUTE FROM jam_.off_hour)) AS work_hour_off_mm,
                        SUM(EXTRACT(HOUR FROM jam_.on_hour) * 60 + EXTRACT(MINUTE FROM jam_.on_hour)) AS work_hour_on_mm
                    FROM
                        tdjamkerjamesin AS jam_
                    WHERE jam_.working_date BETWEEN
                         ? AND ?
                    GROUP BY
                        jam_.machine_id
                ) AS jam ON mac.id = jam.machine_id
                WHERE
                    mac.status = 1
                    AND dep.division_code = ?
            ) AS x )
            as y
                -- WHERE y.persenmesinkerja > 0
            ORDER BY
                y.machine_no
        ', [$diffDay * $minuteOfDay,$startDate, $endDate, $divisionCodeSeitai]);

        return $kadouJikanSeitaiMesin;
    }

    public function getHasilProduksiSeitai($startDate, $endDate)
    {
        $hasilProduksiSeitai = DB::select('
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
            GROUP BY	x.machine_no,x.machine_name,x.department_name
        ', [
            $startDate,
            $endDate,
        ]);

        return $hasilProduksiSeitai;
    }

    public function getLossSeitai($startDate, $endDate, $divisionCodeSeitai)
    {
        $lossSeitai = DB::select('
            SELECT x.* from (
                select
                    ? as division_code,
                    max(mslos.code) as loss_code,
                    max(mslos.name) as loss_name,
                    sum(det.berat_loss) as berat_loss
                from tdproduct_goods as hdr
                inner join tdproduct_goods_loss as det on hdr.id = det.product_goods_id
                inner join mslossseitai as mslos on det.loss_seitai_id = mslos.id
                where hdr.created_on between ? and ?
                and mslos.id <> 1
                group by det.loss_seitai_id
                )as x order BY x.berat_loss DESC
        ', [
            $divisionCodeSeitai,
            $startDate,
            $endDate,
        ]);

        // menghitung berat loss dari loss seitai
        $totalLossSeitai = array_sum(array_map(function ($item) {
            return $item->berat_loss;
        }, $lossSeitai));

        return [
            'lossSeitai' => $lossSeitai,
            'totalLossSeitai' => $totalLossSeitai
        ];
    }

    public function getTopLossSeitai($startDate, $endDate, $divisionCodeSeitai)
    {
        $topLossSeitai = DB::select('
            SELECT x.* from (
                select
                    ? as division_code,
                    max(mslos.code) as loss_code,
                    max(mslos.name) as loss_name,
                    sum(det.berat_loss) as berat_loss
                from tdproduct_goods as hdr
                inner join tdproduct_goods_loss as det on hdr.id = det.product_goods_id
                inner join mslossseitai as mslos on det.loss_seitai_id = mslos.id
                where hdr.created_on between ? and ?
                and mslos.id <> 1
                group by det.loss_seitai_id
                )as x order BY x.berat_loss DESC limit 3
        ', [
            $divisionCodeSeitai,
            $startDate,
            $endDate,
        ]);

        return $topLossSeitai;
    }

    public function getCounterTroubleSeitai($startDate, $endDate)
    {
        $counterTroubleSeitai = DB::select('
            SELECT x.* from (
                select
                    max(mslos.code) as loss_code,
                    max(mslos.name) as loss_name,
                    count(mslos.code) as counterloss
                from tdproduct_goods as hdr
                inner join tdproduct_goods_loss as det on hdr.id = det.product_goods_id
                inner join mslossseitai as mslos on det.loss_seitai_id = mslos.id
                where hdr.created_on between ? and ?
                group by det.loss_seitai_id
                )as x order BY x.loss_name DESC
        ', [
            $startDate,
            $endDate,
        ]);

        return $counterTroubleSeitai;
    }
}
