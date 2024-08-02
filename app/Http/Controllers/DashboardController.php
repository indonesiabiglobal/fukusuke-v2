<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        // dd($kadouJikanMesin);
        return view('dashboard.index');
    }

    /*
    Infure
    */
    public function getkadouJikanInfure(Request $request)
    {
        $filterDate = explode(' to ', $request->filterDate);
        $startDate = Carbon::parse($filterDate[0])->format('Y-m-d');
        if (count($filterDate) == 1) {
            $endDate = Carbon::parse($filterDate[0])->format('Y-m-d');
        } else {
            $endDate = Carbon::parse($filterDate[1])->format('Y-m-d');
        }
        $division_code = 10;
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

        ', [$startDate, $endDate, $division_code]);
        // ', array_merge([$startDate, $endDate, $division_code], $machineNo));

        return response()->json([
            'data' => $kadouJikanInfureMesin
        ]);
    }

    public function getHasilProduksiInfure(Request $request)
    {
        $filterDate = explode(' to ', $request->filterDate);
        $startDate = Carbon::parse($filterDate[0])->format('Y-m-d');
        if (count($filterDate) == 1) {
            $endDate = Carbon::parse($filterDate[0])->format('Y-m-d');
        } else {
            $endDate = Carbon::parse($filterDate[1])->format('Y-m-d');
        }
        // $machineNo = [
        //     '00I01',
        //     '00I02',
        //     '00I03',
        //     '00I04',
        //     '00I05',
        //     '00I06',
        // ];

        $hasilProduksiInfure = DB::select('
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

        return response()->json([
            'data' => $hasilProduksiInfure
        ]);
    }

    public function getLossInfure(Request $request)
    {
        $filterDate = explode(' to ', $request->filterDate);
        $startDate = Carbon::parse($filterDate[0])->format('Y-m-d');
        if (count($filterDate) == 1) {
            $endDate = Carbon::parse($filterDate[0])->startOfDay()->format('Y-m-d H:i:s');
        } else {
            $endDate = Carbon::parse($filterDate[1])->endOfDay()->format('Y-m-d  H:i:s');
        }
        $division_code = 10;

        $kadouJikanInfureMesin = DB::select('
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
            $division_code,
            $startDate,
            $endDate,
        ]);

        return response()->json([
            'data' => $kadouJikanInfureMesin
        ]);
    }

    public function getTopLossInfure(Request $request)
    {
        $filterDate = explode(' to ', $request->filterDate);
        $startDate = Carbon::parse($filterDate[0])->format('Y-m-d');
        if (count($filterDate) == 1) {
            $endDate = Carbon::parse($filterDate[0])->startOfDay()->format('Y-m-d H:i:s');
        } else {
            $endDate = Carbon::parse($filterDate[1])->endOfDay()->format('Y-m-d  H:i:s');
        }
        $division_code = 10;

        $kadouJikanInfureMesin = DB::select('
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
            $division_code,
            $startDate,
            $endDate,
        ]);

        return response()->json([
            'data' => $kadouJikanInfureMesin
        ]);
    }

    public function getCounterTroubleInfure(Request $request)
    {
        $filterDate = explode(' to ', $request->filterDate);
        $startDate = Carbon::parse($filterDate[0])->format('Y-m-d');
        if (count($filterDate) == 1) {
            $endDate = Carbon::parse($filterDate[0])->startOfDay()->format('Y-m-d H:i:s');
        } else {
            $endDate = Carbon::parse($filterDate[1])->endOfDay()->format('Y-m-d  H:i:s');
        }

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

        return response()->json([
            'data' => $counterTroubleInfure
        ]);
    }

    /*
    SEITAI
    */
    public function getkadouJikanSeitai(Request $request)
    {
        $filterDate = explode(' to ', $request->filterDate);
        $startDate = Carbon::parse($filterDate[0])->format('Y-m-d');
        if (count($filterDate) == 1) {
            $endDate = Carbon::parse($filterDate[0])->format('Y-m-d');
        } else {
            $endDate = Carbon::parse($filterDate[1])->format('Y-m-d');
        }
        $division_code = 20;

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
            $division_code
        ]);

        return response()->json([
            'data' => $kadouJikanSeitaiMesin
        ]);
    }

    public function getLossSeitai(Request $request)
    {
        $filterDate = explode(' to ', $request->filterDate);
        $startDate = Carbon::parse($filterDate[0])->format('Y-m-d');
        if (count($filterDate) == 1) {
            $endDate = Carbon::parse($filterDate[0])->startOfDay()->format('Y-m-d H:i:s');
        } else {
            $endDate = Carbon::parse($filterDate[1])->endOfDay()->format('Y-m-d  H:i:s');
        }
        $division_code = 20;

        $kadouJikanInfureMesin = DB::select('
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
            $division_code,
            $startDate,
            $endDate,
        ]);

        return response()->json([
            'data' => $kadouJikanInfureMesin
        ]);
    }

    public function getHasilProduksiSeitai(Request $request)
    {
        $filterDate = explode(' to ', $request->filterDate);
        $startDate = Carbon::parse($filterDate[0])->format('Y-m-d');
        if (count($filterDate) == 1) {
            $endDate = Carbon::parse($filterDate[0])->format('Y-m-d');
        } else {
            $endDate = Carbon::parse($filterDate[1])->format('Y-m-d');
        }

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

        return response()->json([
            'data' => $hasilProduksiSeitai
        ]);
    }

    public function getTopLossSeitai(Request $request)
    {
        $filterDate = explode(' to ', $request->filterDate);
        $startDate = Carbon::parse($filterDate[0])->format('Y-m-d');
        if (count($filterDate) == 1) {
            $endDate = Carbon::parse($filterDate[0])->startOfDay()->format('Y-m-d H:i:s');
        } else {
            $endDate = Carbon::parse($filterDate[1])->endOfDay()->format('Y-m-d  H:i:s');
        }
        $division_code = 20;

        $kadouJikanSeitaiMesin = DB::select('
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
            $division_code,
            $startDate,
            $endDate,
        ]);

        return response()->json([
            'data' => $kadouJikanSeitaiMesin
        ]);
    }

    public function getCounterTroubleSeitai(Request $request)
    {
        $filterDate = explode(' to ', $request->filterDate);
        $startDate = Carbon::parse($filterDate[0])->format('Y-m-d');
        if (count($filterDate) == 1) {
            $endDate = Carbon::parse($filterDate[0])->startOfDay()->format('Y-m-d H:i:s');
        } else {
            $endDate = Carbon::parse($filterDate[1])->endOfDay()->format('Y-m-d  H:i:s');
        }
        $division_code = 20;

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

        return response()->json([
            'data' => $counterTroubleSeitai
        ]);
    }

}
