<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Models\MsMachine;
use App\Models\MsProduct;
use App\Models\MsLossInfure;
use App\Models\TdOrderLpk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MobileMasterController extends Controller
{
    /**
     * GET /api/mobile/master/machines
     */
    public function machines()
    {
        $data = MsMachine::select('id', 'machineno', 'machinename', 'department_id')
            ->whereIn('department_id', [10, 12, 15, 2, 4])
            ->orderBy('machineno')
            ->get();

        return ApiResponse::success($data);
    }

    /**
     * GET /api/mobile/master/products
     */
    public function products()
    {
        $data = MsProduct::select('id', 'name', 'code', 'code_alias')
            ->orderBy('code')
            ->get();

        return ApiResponse::success($data);
    }

    /**
     * GET /api/mobile/master/loss-types
     */
    public function lossTypes()
    {
        $data = MsLossInfure::select('id', 'code', 'name')
            ->orderBy('code')
            ->get();

        return ApiResponse::success($data);
    }

    /**
     * GET /api/mobile/master/lpk/{lpk_no}
     */
    public function lpkByNo($lpk_no)
    {
        $data = DB::table('tdorderlpk as tolp')
            ->select(
                'tolp.id',
                'tolp.lpk_no',
                'tolp.lpk_date',
                'tolp.panjang_lpk',
                'tolp.qty_gentan',
                'tolp.total_assembly_line',
                'mp.id as product_id',
                'mp.name as product_name',
                'mp.code as product_code',
            )
            ->leftJoin('msproduct as mp', 'mp.id', '=', 'tolp.product_id')
            ->where('tolp.lpk_no', $lpk_no)
            ->first();

        if (! $data) {
            return ApiResponse::notFound('LPK tidak ditemukan.');
        }

        return ApiResponse::success($data);
    }
}
