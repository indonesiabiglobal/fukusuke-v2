<?php

namespace App\Http\Controllers;

use App\Http\Livewire\MasterTabel\WorkingShift;
use App\Models\MsDepartment;
use App\Models\MsWorkingShift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPSTORM_META\map;

class DashboardController extends Controller
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
            $startDate = Carbon::now()->subMonth()->format('d-m-Y 00:00:00');
            $endDate = Carbon::now()->format('d-m-Y 23:59:59');
            $requestFilterDate = $startDate . ' to ' . $endDate;
        }
        $divisionCodeInfure = MsDepartment::where('name', 'INFURE')->first()->division_code;
        $divisionCodeSeitai = MsDepartment::where('name', 'SEITAI')->first()->division_code;


        $data = [
            'filterDate' => $requestFilterDate,

            // Infure
            'listMachineInfure' => $this->getListMachineInfure($startDate, $endDate, $divisionCodeInfure),
            'kadouJikanInfureMesin' => $this->getKadouJikanInfure($startDate, $endDate, $divisionCodeInfure),
            'topLossInfure' => $this->getTopLossInfure($startDate, $endDate, $divisionCodeInfure),

            // Seitai
            'listMachineSeitai' => $this->getListMachineSeitai($startDate, $endDate, $divisionCodeSeitai),
            'kadouJikanSeitaiMesin' => $this->getkadouJikanSeitai($startDate, $endDate, $divisionCodeSeitai),
            'topLossSeitai' => $this->getTopLossSeitai($startDate, $endDate, $divisionCodeSeitai),
        ];
        return view('dashboard.index', $data);
    }

    public function ppic(Request $request)
    {
        if (isset($request->filterDate)) {
            $requestFilterDate = $request->filterDate;
            $filterDate = explode(' to ', $request->filterDate);
            $startDate = Carbon::parse($filterDate[0])->format('Y-m-d');
            if (count($filterDate) == 1) {
                $endDate = Carbon::parse($filterDate[0])->format('Y-m-d');
            } else {
                $endDate = Carbon::parse($filterDate[1])->format('Y-m-d');
            }
        } else {
            $startDate = Carbon::now()->subMonth()->format('d-m-Y');
            $endDate = Carbon::now()->format('d-m-Y');
            $requestFilterDate = $startDate . ' to ' . $endDate;
        }
        $divisionCodeInfure = MsDepartment::where('name', 'INFURE')->first()->division_code;
        $divisionCodeSeitai = MsDepartment::where('name', 'SEITAI')->first()->division_code;

        $data = [
            'filterDate' => $requestFilterDate,

            'pertipeinfure' => $this->getPerTipeInfure(),
            'pertipeseitai' => $this->getPerTipeSeitai(),
            'hasilproduksiinfure' => $this->getHasilProduksiInfure(),
            'hasilproduksiseitai' => $this->getHasilProduksiSeitai(),
            

            'listMachineInfure' => $this->getListMachineInfure($startDate, $endDate, $divisionCodeInfure),
            'kadouJikanInfureMesin' => $this->getKadouJikanInfure($startDate, $endDate, $divisionCodeInfure),
            'topLossInfure' => $this->getTopLossInfure($startDate, $endDate, $divisionCodeInfure),
            'listMachineSeitai' => $this->getListMachineSeitai($startDate, $endDate, $divisionCodeSeitai),
            'kadouJikanSeitaiMesin' => $this->getkadouJikanSeitai($startDate, $endDate, $divisionCodeSeitai),
            'topLossSeitai' => $this->getTopLossSeitai($startDate, $endDate, $divisionCodeSeitai),

        ];
        return view('dashboard.dashboard-ppic', $data);
    }

    public function qc(Request $request)
    {
        if (isset($request->filterDate)) {
            $requestFilterDate = $request->filterDate;
            $filterDate = explode(' to ', $request->filterDate);
            $startDate = Carbon::parse($filterDate[0])->format('Y-m-d');
            if (count($filterDate) == 1) {
                $endDate = Carbon::parse($filterDate[0])->format('Y-m-d');
            } else {
                $endDate = Carbon::parse($filterDate[1])->format('Y-m-d');
            }
        } else {
            $startDate = Carbon::now()->subMonth()->format('d-m-Y');
            $endDate = Carbon::now()->format('d-m-Y');
            $requestFilterDate = $startDate . ' to ' . $endDate;
        }

        $data = [
            'filterDate' => $requestFilterDate,

            'totalprodukkenpin' => $this->getTotalProdukKenpin($startDate, $endDate),
            'jenisprodukkenpin' => $this->getJenisProdukKenpin($startDate, $endDate,),

        ];
        return view('dashboard.dashboard-qc', $data);
    }

    // INFURE
    public function getListMachineInfure($startDate, $endDate, $divisionCodeInfure)
    {
        $today = Carbon::now();
        $shiftSekarang = MsWorkingShift::select('work_shift')
            ->where('work_hour_from', '<=', $today->format('H:i:s'))
            ->where('work_hour_till', '>=', $today->format('H:i:s'))
            ->where('status', 1)
            ->first();
        $listMachineInfure = DB::select('
        SELECT x.* from (
            SELECT
            RIGHT(mac.machineno, 2) AS machine_no,
                mac.machineno AS machine_name,
                dep.division_code,
                dep."id" as department_id,
                dep."name" as department_name,
                    CASE
                    WHEN jam.work_shift= ? THEN
                    1 ELSE 0
                END AS is_working_shift
            FROM
                msmachine AS mac
            INNER JOIN
                msdepartment AS dep ON mac.department_id = dep.id
            LEFT JOIN (
                SELECT
                    jam_.machine_id,jam_.work_shift

                FROM
                    tdjamkerjamesin AS jam_
                WHERE
                    jam_.working_date = ?
                    AND jam_.work_shift= ?
                GROUP BY
                    jam_.machine_id,jam_.work_shift
            ) AS jam ON mac.id = jam.machine_id
            WHERE
                mac.status = 1
                    and dep.division_code=? ) as x
        GROUP BY x.machine_no,x.machine_name,
        x.division_code,x.department_id,x.department_name,x.is_working_shift ORDER BY x.machine_no
        ', [$shiftSekarang, $today->format('Y-m-d'), $shiftSekarang,$divisionCodeInfure]);
        // [$shiftSekarang, $divisionCodeInfure, $today->format('Y-m-d')]);

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
                    mac.machineno AS machine_name,
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
                WHERE y.persenmesinkerja > 0
            ORDER BY
                y.machine_no

        ', [$diffDay * $minuteOfDay,$startDate, $endDate, $divisionCodeInfure]);
        // ', array_merge([$startDate, $endDate, $division_code], $machineNo));

        return $kadouJikanInfureMesin;
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

    /*
    SEITAI
    */
    public function getListMachineSeitai($startDate, $endDate, $divisionCodeSeitai)
    {
        $today = Carbon::now();
        $shiftSekarang = MsWorkingShift::select('work_shift')
            ->where('work_hour_from', '<=', $today->format('H:i:s'))
            ->where('work_hour_till', '>=', $today->format('H:i:s'))
            ->where('status', 1)
            ->first();
        $listMachineSeitai = DB::select('
        SELECT x.* from (
            SELECT
            RIGHT(mac.machineno, 2) AS machine_no,
                mac.machineno AS machine_name,
                dep.division_code,
                dep."id" as department_id,
                dep."name" as department_name,
                    CASE
                    WHEN jam.work_shift= ? THEN
                    1 ELSE 0
                END AS is_working_shift
            FROM
                msmachine AS mac
            INNER JOIN
                msdepartment AS dep ON mac.department_id = dep.id
            LEFT JOIN (
                SELECT
                    jam_.machine_id,jam_.work_shift

                FROM
                    tdjamkerjamesin AS jam_
                WHERE
                    jam_.working_date = ?
                    AND jam_.work_shift= ?
                GROUP BY
                    jam_.machine_id,jam_.work_shift
            ) AS jam ON mac.id = jam.machine_id
            WHERE
                mac.status = 1
                    and dep.division_code=? ) as x
        GROUP BY x.machine_no,x.machine_name,
        x.division_code,x.department_id,x.department_name,x.is_working_shift ORDER BY x.machine_no

        ', [$shiftSekarang, $today->format('Y-m-d'), $shiftSekarang,$divisionCodeSeitai]);
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
                    mac.machineno AS machine_name,
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
                WHERE y.persenmesinkerja > 0
            ORDER BY
                y.machine_no
        ', [$diffDay * $minuteOfDay,$startDate, $endDate, $divisionCodeSeitai]);

        return $kadouJikanSeitaiMesin;
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
    public function getTotalProdukKenpin($startDate, $endDate)
    {
        $totalProdukKenpin = DB::select("
        select
            '10' as division_code,
            prd.name as product_code,
            cast(count(kenpin.product_assembly_id) as varchar) || ' gentan' as jumlahloss
        from tdkenpin_assembly as kenpinhdr
        left join tdkenpin_assembly_detail as kenpin on kenpinhdr.id = kenpin.kenpin_assembly_id
        inner join tdproduct_assembly as asyx on kenpin.product_assembly_id = asyx.id
        inner join msproduct as prd on asyx.product_id = prd.id
        where kenpinhdr.kenpin_date = ?
        group by prd.name

        union all
        select
            '20' as division_code,
            prd.name as product_code,
            cast(sum(kenpinhdr.qty_loss / (case when prd.case_box_count = 0 then 1000 else prd.case_box_count end)) as varchar) || ' box' as jumlahloss
        from tdkenpin_goods as kenpinhdr
        left join tdkenpin_goods_detail as kenpin on kenpinhdr.id = kenpin.kenpin_goods_id
        left join tdproduct_goods as gdsx on kenpin.product_goods_id = gdsx.id
        left join msproduct as prd on kenpinhdr.product_id = prd.id
        where kenpinhdr.kenpin_date = ?
        and  kenpinhdr.status_kenpin = 2
        group by prd.name
        ", [
            $startDate,
            $endDate,
        ]);

        return $totalProdukKenpin;
    }
    public function getJenisProdukKenpin($startDate, $endDate)
    {
        $jenisProdukKenpin = DB::select("
        select
            '10' as division_code,
            kenpinhdr.remark as jenis,
            cast(count(kenpin.product_assembly_id) as varchar) || ' gentan' as jumlahloss
        from tdkenpin_assembly as kenpinhdr
        left join tdkenpin_assembly_detail as kenpin on kenpinhdr.id = kenpin.kenpin_assembly_id
        inner join tdproduct_assembly as asyx on kenpin.product_assembly_id = asyx.id
        inner join msproduct as prd on asyx.product_id = prd.id
        where kenpinhdr.kenpin_date = ?
        group by kenpinhdr.remark
        union all
        select
            '20' as division_code,
                kenpinhdr.remark as jenis,
            cast(sum(kenpinhdr.qty_loss / (case when prd.case_box_count = 0 then 1000 else prd.case_box_count end)) as varchar) || ' box' as jumlahloss
        from tdkenpin_goods as kenpinhdr
        left join tdkenpin_goods_detail as kenpin on kenpinhdr.id = kenpin.kenpin_goods_id
        left join tdproduct_goods as gdsx on kenpin.product_goods_id = gdsx.id
        left join msproduct as prd on kenpinhdr.product_id = prd.id
        where kenpinhdr.kenpin_date = ?
        group by kenpinhdr.remark
        ", [
            $startDate,
            $endDate,
        ]);

        return $jenisProdukKenpin;
    }
    public function getPerTipeInfure()
    {
        $perTipeInfure = DB::select("
        SELECT MAX
            ( prTip.code ) AS product_type_code,
            MAX ( prTip.NAME ) AS product_type_name,
            round(SUM ( asy.berat_produksi )) AS berat_produksi,
            round(SUM ( asy.panjang_produksi )) AS panjang_produksi
        FROM
            tdProduct_Assembly AS asy
            INNER JOIN msProduct AS prd ON asy.product_id = prd.
            ID INNER JOIN msProduct_type AS prTip ON prd.product_type_id = prTip.ID 
        WHERE
            asy.production_date BETWEEN '2024-07-01 00:00:00' 
            AND '2024-07-04 23:59:00' 
        GROUP BY
            prTip.ID
        ");

        return $perTipeInfure;
    }
    public function getPerTipeSeitai()
    {
        $perTipeSeitai = DB::select("
        SELECT 
            MAX(prT.code) AS product_type_code,
            MAX(prT.name) AS product_type_name,
            round(SUM(good.qty_produksi * prd.unit_weight * 0.001)) AS berat_produksi,
            round(SUM(good.seitai_berat_loss) - COALESCE(SUM(ponsu.berat_loss), 0)) AS seitai_berat_loss
        FROM tdProduct_Goods AS good 
        LEFT JOIN (
            SELECT 
                los_.product_goods_id, 
                SUM(los_.berat_loss) AS berat_loss
            FROM tdProduct_Goods_Loss AS los_
            WHERE los_.loss_seitai_id = 1 -- ponsu
            GROUP BY los_.product_goods_id
        ) ponsu ON good.id = ponsu.product_goods_id
        INNER JOIN msProduct AS prd ON good.product_id = prd.id 
        INNER JOIN msProduct_type AS prT ON prd.product_type_id = prT.id 
        WHERE good.production_date BETWEEN '2024-07-04 00:00:00' AND '2024-07-04 23:59:00'
        GROUP BY prT.id;
        ");

        return $perTipeSeitai;
    }
    public function getHasilProduksiInfure()
    {
        $hasilProduksiInfure = DB::select("
        SELECT x.bulan, round(x.berat_produksi) as berat_produksi from(
        SELECT 
            to_char(asy.production_date,'FMMonth YYYY') as bulan,
            SUM(asy.berat_standard) AS berat_standard,
            SUM(asy.berat_produksi) AS berat_produksi,
            SUM(asy.infure_cost) AS infure_cost,
            SUM(asy.infure_berat_loss) AS infure_berat_loss,
            SUM(asy.panjang_produksi) AS panjang_produksi,
            SUM(asy.panjang_printing_inline) AS panjang_printing_inline,
            SUM(asy.infure_cost_printing) AS infure_cost_printing
        FROM tdProduct_Assembly AS asy
        INNER JOIN msProduct AS prd ON asy.product_id = prd.id 
        WHERE (asy.production_date BETWEEN '2024-08-04 00:00:00' AND '2024-08-04 23:59:00') or 
        (asy.production_date BETWEEN '2023-08-04 00:00:00' AND '2023-08-04 23:59:00')
        GROUP BY to_char(asy.production_date,'FMMonth YYYY')
        ) as x ORDER BY x.bulan;
        ");

        return $hasilProduksiInfure;
    }
    public function getHasilProduksiSeitai()
    {
        $hasilProduksiSeitai = DB::select("
        SELECT 
            to_char(good.production_date,'FMMonth YYYY') as bulan,
            round(SUM(good.qty_produksi * prd.unit_weight * 0.001)) AS berat_produksi
        FROM tdProduct_Goods AS good 
        LEFT JOIN (
            SELECT 
                los_.product_goods_id, 
                SUM(los_.berat_loss) AS berat_loss
            FROM tdProduct_Goods_Loss AS los_
            WHERE los_.loss_seitai_id = 1 -- ponsu
            GROUP BY los_.product_goods_id
        ) ponsu ON good.id = ponsu.product_goods_id
        INNER JOIN msProduct AS prd ON good.product_id = prd.id 
        WHERE (good.production_date BETWEEN '2023-08-04 00:00:00' AND '2023-08-04 23:59:00') 
        or (good.production_date BETWEEN '2024-08-04 00:00:00' AND '2024-08-04 23:59:00')
        GROUP BY to_char(good.production_date,'FMMonth YYYY')
        ");

        return $hasilProduksiSeitai;
    }
}
