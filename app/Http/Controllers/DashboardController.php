<?php

namespace App\Http\Controllers;

use App\Models\MsDepartment;
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

            // Infure
            'listMachineInfure' => $this->getListMachineInfure($startDate, $endDate, $divisionCodeInfure),
            'kadouJikanInfureMesin' => $this->getKadouJikanInfure($startDate, $endDate, $divisionCodeInfure),
            'hasilProduksiInfure' => $this->getHasilProduksiInfure($startDate, $endDate),
            'lossInfure' => $this->getLossInfure($startDate, $endDate, $divisionCodeInfure),
            'topLossInfure' => $this->getTopLossInfure($startDate, $endDate, $divisionCodeInfure),
            'counterTroubleInfure' => $this->getCounterTroubleInfure($startDate, $endDate),

            // Seitai
            'listMachineSeitai' => $this->getListMachineSeitai($startDate, $endDate, $divisionCodeSeitai),
            'kadouJikanSeitaiMesin' => $this->getkadouJikanSeitai($startDate, $endDate, $divisionCodeSeitai),
            'hasilProduksiSeitai' => $this->getHasilProduksiSeitai($startDate, $endDate),
            'lossSeitai' => $this->getLossSeitai($startDate, $endDate, $divisionCodeSeitai),
            'topLossSeitai' => $this->getTopLossSeitai($startDate, $endDate, $divisionCodeSeitai),
            'counterTroubleSeitai' => $this->getCounterTroubleSeitai($startDate, $endDate),

        ];
        return view('dashboard.index', $data);
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
        // $machineNo = [
        //     '00I01',
        //     '00I02',
        //     '00I03',
        //     '00I04',
        //     '00I05',
        //     '00I06',
        // ];

        $kadouJikanInfureMesin = DB::select('
        SELECT y.* FROM (
            SELECT x.*,round(x.work_hour_on_mm/1440*100,2) as persenmesinkerja from (
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

        ', [$startDate, $endDate, $divisionCodeInfure]);
        // ', array_merge([$startDate, $endDate, $division_code], $machineNo));

        return $kadouJikanInfureMesin;
    }

    public function getHasilProduksiInfure($startDate, $endDate)
    {
        // $machineNo = [
        //     '00I01',
        //     '00I02',
        //     '00I03',
        //     '00I04',
        //     '00I05',
        //     '00I06',
        // ];

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
            ) as x order BY x.counterloss DESC
        ', [
            $startDate,
            $endDate,
        ]);

        return $counterTroubleInfure;
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
        // $machineNo = [
        //     '00I01',
        //     '00I02',
        //     '00I03',
        //     '00I04',
        //     '00I05',
        //     '00I06',
        // ];

        $kadouJikanSeitaiMesin = DB::select('
        SELECT y.* FROM (
            SELECT x.*, ROUND(x.work_hour_on_mm / 1440 * 100, 2) AS persenmesinkerja
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
        ', [
            $startDate,
            $endDate,
            $divisionCodeSeitai
        ]);

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
                    sum(pa.qty_produksi) as totalpanjangproduksi
            from tdproduct_goods as pa
            left join msmachine as mac on mac.id=pa.machine_id
            left join msdepartment as dep on mac.department_id = dep.id
            where pa.created_on between ? and ?
            GROUP BY pa.created_on, right(mac.machineno, 2),
                mac.machineno,
                dep.name
            ) as x
            GROUP BY x.machine_no,x.machine_name,x.department_name
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

        // menghitung berat loss dari loss infure
        $totalLossInfure = array_sum(array_map(function ($item) {
            return $item->berat_loss;
        }, $lossSeitai));

        return [
            'lossSeitai' => $lossSeitai,
            'totalLossSeitai' => $totalLossInfure
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
                )as x order BY x.counterloss DESC limit 10
        ', [
            $startDate,
            $endDate,
        ]);

        return $counterTroubleSeitai;
    }
}
