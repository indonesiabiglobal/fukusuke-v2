<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MobileLabelGentanController extends Controller
{
    /**
     * GET /api/mobile/label-gentan
     * Params: lpk_no (required), gentan_no (optional)
     *
     * When gentan_no is provided, returns full label data.
     * Without gentan_no, returns LPK header info only.
     */
    public function show(Request $request)
    {
        $request->validate([
            'lpk_no'   => 'required|string|size:10',
            'gentan_no' => 'nullable|string|max:20',
        ]);

        $lpk_no   = $request->get('lpk_no');
        $gentan_no = $request->get('gentan_no');

        // Fetch LPK header
        $lpk = DB::table('tdorderlpk as tod')
            ->join('msproduct as mp', 'mp.id', '=', 'tod.product_id')
            ->select(
                'tod.id as lpk_id',
                'tod.lpk_no',
                'tod.lpk_date',
                'tod.panjang_lpk',
                'tod.qty_gentan',
                'tod.qty_lpk',
                'tod.total_assembly_line',
                'tod.product_panjanggulung',
                'mp.id as product_id',
                'mp.code',
                'mp.name as product_name',
                'mp.code_alias',
            )
            ->where('tod.lpk_no', $lpk_no)
            ->first();

        if (! $lpk) {
            return ApiResponse::notFound('LPK tidak ditemukan.');
        }

        // If no gentan_no is specified, return header only
        if (! $gentan_no) {
            return ApiResponse::success(['lpk' => $lpk, 'gentan' => null]);
        }

        // Fetch full gentan detail for label printing
        $gentan = DB::table('tdproduct_assembly as tpa')
            ->join('tdorderlpk as tod', 'tpa.lpk_id', '=', 'tod.id')
            ->join('msproduct as mp', 'mp.id', '=', 'tod.product_id')
            ->leftJoin('msworkingshift as msw', 'msw.id', '=', 'tpa.work_shift')
            ->join('msmachine as msm', 'msm.id', '=', 'tpa.machine_id')
            ->join('msemployee as mse', 'mse.id', '=', 'tpa.employee_id')
            ->select(
                'tpa.id as produk_asembly_id',
                'tod.lpk_no',
                'tod.lpk_date',
                'tpa.production_date',
                'tpa.work_shift',
                'tpa.work_hour',
                'tpa.gentan_no',
                'tpa.nomor_han',
                'tpa.panjang_produksi',
                'tpa.berat_produksi',
                'tpa.berat_standard',
                'tpa.status_production',
                DB::raw('(tod.total_assembly_line - tod.panjang_lpk) as selisih'),
                'mp.code',
                'mp.code_alias',
                'mp.name as product_name',
                'mp.product_type_code',
                'tod.panjang_lpk',
                'tod.total_assembly_line',
                'tod.product_panjanggulung',
                'msm.machineno',
                'mse.employeeno as nik',
                'mse.empname',
            )
            ->where('tod.lpk_no', $lpk_no)
            ->where('tpa.gentan_no', $gentan_no)
            ->first();

        if (! $gentan) {
            return ApiResponse::notFound('Nomor Gentan tidak ditemukan.');
        }

        return ApiResponse::success(['lpk' => $lpk, 'gentan' => $gentan]);
    }
}
