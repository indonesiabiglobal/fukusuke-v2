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
        return view('dashboard.dashboard-qc', $data);
    }

    // INFURE
    public function getListMachineInfure($startDate, $endDate, $divisionCodeInfure)
    {
        $today = Carbon::parse('2024-07-09');
        $shiftSekarang = MsWorkingShift::select('work_shift')
            ->where('work_hour_from', '<=', $today->format('H:i:s'))
            ->where('work_hour_till', '>=', $today->format('H:i:s'))
            ->where('status', 1)
            ->first();
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
}
