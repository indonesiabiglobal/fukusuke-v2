<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MobileChecklistController extends Controller
{
    /**
     * GET /api/mobile/checklist-infure
     */
    public function index(Request $request)
    {
        $tglAwal      = $request->get('tglAwal',  Carbon::today()->format('Y-m-d'));
        $tglAkhir     = $request->get('tglAkhir', Carbon::today()->format('Y-m-d'));
        $jamAwal      = $request->get('jamAwal',  '00:00');
        $jamAkhir     = $request->get('jamAkhir', '23:59');
        $transaksi    = $request->get('transaksi', 1);
        $jenisReport  = $request->get('jenisReport', 'checklist');
        $noprosesFrom = $request->get('noprosesFrom', '');
        $noprosesTo   = $request->get('noprosesTo',   '');

        $query = DB::table('tdproduct_assembly AS tdpa')
            ->select([
                'tdpa.id',
                'tdpa.production_no',
                'tdpa.production_date',
                'tdpa.work_shift',
                'tdpa.work_hour',
                'tdpa.machine_id',
                'tdpa.status_production',
                'tdpa.panjang_produksi',
                'tdpa.berat_standard',
                'tdpa.berat_produksi',
                DB::raw('CASE WHEN tdpa.berat_standard = 0 THEN 0 ELSE ROUND(tdpa.berat_produksi / tdpa.berat_standard * 100, 2) END AS rasio_produksi'),
                'tdpa.nomor_han',
                'tdpa.gentan_no',
                'tdpa.created_on',
                'tdol.lpk_no',
                'msm.machineno',
                'msp.name AS product_name',
                'msp.code AS product_code',
            ])
            ->join('tdorderlpk AS tdol', 'tdpa.lpk_id', '=', 'tdol.id')
            ->join('msmachine AS msm', 'msm.id', '=', 'tdpa.machine_id')
            ->leftJoin('msproduct AS msp', 'msp.id', '=', 'tdol.product_id');

        // Date + time filtering
        if ($transaksi == 2) {
            $query->where(DB::raw("DATE(tdpa.created_on)"), '>=', $tglAwal)
                  ->where(DB::raw("DATE(tdpa.created_on)"), '<=', $tglAkhir)
                  ->where(DB::raw("TIME(tdpa.work_hour)"), '>=', $jamAwal)
                  ->where(DB::raw("TIME(tdpa.work_hour)"), '<=', $jamAkhir);
        } else {
            $query->whereDate('tdpa.production_date', '>=', $tglAwal)
                  ->whereDate('tdpa.production_date', '<=', $tglAkhir)
                  ->where(DB::raw("TIME(tdpa.work_hour)"), '>=', $jamAwal)
                  ->where(DB::raw("TIME(tdpa.work_hour)"), '<=', $jamAkhir);
        }

        if ($noprosesFrom) {
            $query->where('tdpa.production_no', '>=', $noprosesFrom);
        }
        if ($noprosesTo) {
            $query->where('tdpa.production_no', '<=', $noprosesTo);
        }

        $data = $query->orderBy('tdpa.production_no')->limit(500)->get();

        // For loss report: attach total loss per record
        if ($jenisReport === 'loss') {
            $ids = $data->pluck('id');
            $lossMap = DB::table('tdproduct_assembly_loss')
                ->select('product_assembly_id', DB::raw('SUM(berat_loss) as total_loss'))
                ->whereIn('product_assembly_id', $ids)
                ->groupBy('product_assembly_id')
                ->pluck('total_loss', 'product_assembly_id');

            $data = $data->map(function ($row) use ($lossMap) {
                $row->total_loss = $lossMap[$row->id] ?? 0;
                return $row;
            });
        }

        return ApiResponse::success($data->values());
    }
}
