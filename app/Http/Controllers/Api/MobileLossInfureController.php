<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Models\TdProductAssemblyLoss;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MobileLossInfureController extends Controller
{
    /**
     * GET /api/mobile/loss-infure
     * Returns tdproduct_assembly records (same as web LossInfureController),
     * filtered identically to MobileNippoInfureController.
     */
    public function index(Request $request)
    {
        $tglMasuk  = $request->get('tglMasuk',  Carbon::today()->format('Y-m-d'));
        $tglKeluar = $request->get('tglKeluar', Carbon::today()->format('Y-m-d'));
        $transaksi = $request->get('transaksi', 1);
        $lpk_no    = $request->get('lpk_no', '');
        $status    = $request->get('status', '');
        $search    = $request->get('searchTerm', '');

        $query = DB::table('tdproduct_assembly AS tda')
            ->select([
                'tda.id AS id',
                'tda.production_no AS production_no',
                'tda.production_date AS production_date',
                'tda.employee_id AS employee_id',
                'tda.work_shift AS work_shift',
                'tda.work_hour AS work_hour',
                'tda.machine_id AS machine_id',
                'tda.lpk_id AS lpk_id',
                'tda.product_id AS product_id',
                'tda.panjang_produksi AS panjang_produksi',
                'tda.berat_standard AS berat_standard',
                'tda.berat_produksi AS berat_produksi',
                DB::raw('CASE WHEN tda.berat_standard = 0 THEN 0 ELSE (tda.berat_produksi / tda.berat_standard * 100) END AS rasio_produksi'),
                DB::raw('tdol.total_assembly_line - tdol.panjang_lpk AS selisih'),
                'tda.nomor_han AS nomor_han',
                'tda.gentan_no AS gentan_no',
                'tda.seq_no AS seq_no',
                'tda.status_production AS status_production',
                'tda.status_kenpin AS status_kenpin',
                'tda.infure_berat_loss AS infure_berat_loss',
                'tda.created_by AS created_by',
                'tda.created_on AS created_on',
                'tda.updated_by AS updated_by',
                'tda.updated_on AS updated_on',
                'tdol.lpk_no AS lpk_no',
                'tdol.lpk_date AS lpk_date',
                'tdol.panjang_lpk AS panjang_lpk',
                'tdol.qty_gentan AS qty_gentan',
                'tdol.total_assembly_line AS total_assembly_line',
                'msm.machineno',
                'msm.machinename',
                'tdo.product_code',
                'mp.name AS product_name',
            ])
            ->join('tdorderlpk AS tdol', 'tda.lpk_id', '=', 'tdol.id')
            ->leftJoin('msproduct AS mp', 'mp.id', '=', 'tdol.product_id')
            ->join('msmachine AS msm', 'msm.id', '=', 'tda.machine_id')
            ->join('tdorder AS tdo', 'tdol.order_id', '=', 'tdo.id');

        if ($transaksi == 2) {
            if ($tglMasuk && $tglMasuk !== '') {
                $query->where('tda.created_on', '>=', $tglMasuk . ' 00:00:00');
            }
            if ($tglKeluar && $tglKeluar !== '') {
                $query->where('tda.created_on', '<=', $tglKeluar . ' 23:59:59');
            }
        } else {
            if ($tglMasuk && $tglMasuk !== '') {
                $query->where('tda.production_date', '>=', $tglMasuk . ' 00:00:00');
            }
            if ($tglKeluar && $tglKeluar !== '') {
                $query->where('tda.production_date', '<=', $tglKeluar . ' 23:59:59');
            }
        }

        if ($lpk_no && strlen($lpk_no) == 10) {
            $query->where('tdol.lpk_no', 'ilike', "%{$lpk_no}%");
        }

        if ($status !== '' && $status !== null) {
            if ($status == 0) {
                $query->where('tda.status_production', 0)
                      ->where('tda.status_kenpin', 0);
            } elseif ($status == 1) {
                $query->where('tda.status_production', 1);
            } elseif ($status == 2) {
                $query->where('tda.status_kenpin', 1);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('tda.production_no', 'ilike', "%{$search}%")
                  ->orWhere('tdol.lpk_no', 'ilike', "%{$search}%")
                  ->orWhere('tda.nomor_han', 'ilike', "%{$search}%");
            });
        }

        $perPage   = min((int) $request->get('per_page', 20), 100);
        $paginator = $query->orderByDesc('tda.created_on')->paginate($perPage);

        return ApiResponse::paginated($paginator);
    }

    /**
     * GET /api/mobile/loss-infure/{id}
     * Returns a single tdproduct_assembly_loss detail record (used by EditLoss).
     */
    public function show($id)
    {
        $data = DB::table('tdproduct_assembly_loss AS tal')
            ->select([
                'tal.*',
                'msi.code AS loss_infure_code',
                'msi.name AS loss_infure_name',
                'tda.production_no',
                'tdol.lpk_no',
                'msm.machineno',
                'mp.name AS product_name',
            ])
            ->join('tdproduct_assembly AS tda', 'tda.id', '=', 'tal.product_assembly_id')
            ->join('tdorderlpk AS tdol', 'tdol.id', '=', 'tda.lpk_id')
            ->join('msmachine AS msm', 'msm.id', '=', 'tda.machine_id')
            ->leftJoin('msproduct AS mp', 'mp.id', '=', 'tdol.product_id')
            ->join('mslossinfure AS msi', 'msi.id', '=', 'tal.loss_infure_id')
            ->where('tal.id', $id)
            ->first();

        if (! $data) {
            return ApiResponse::notFound();
        }

        return ApiResponse::success($data);
    }

    /**
     * POST /api/mobile/loss-infure
     * Creates a tdproduct_assembly_loss detail record (used by AddLoss screen).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'produk_asemblyid' => 'required|integer|exists:tdproduct_assembly,id',
            'loss_infure_id'   => 'required|integer|exists:mslossinfure,id',
            'berat'            => 'required|numeric|min:0',
            'frekuensi'        => 'required|integer|min:0',
            'keterangan'       => 'nullable|string|max:500',
        ]);

        $loss = new TdProductAssemblyLoss();
        $loss->product_assembly_id = $validated['produk_asemblyid'];
        $loss->loss_infure_id      = $validated['loss_infure_id'];
        $loss->berat               = $validated['berat'];
        $loss->frekuensi           = $validated['frekuensi'];
        $loss->berat_loss          = $validated['berat'] * $validated['frekuensi'];
        $loss->keterangan          = $validated['keterangan'] ?? null;
        $loss->save();

        return ApiResponse::created(['id' => $loss->id]);
    }

    /**
     * PUT /api/mobile/loss-infure/{id}
     * Updates a tdproduct_assembly_loss detail record (used by EditLoss screen).
     */
    public function update(Request $request, $id)
    {
        $loss = TdProductAssemblyLoss::findOrFail($id);

        $validated = $request->validate([
            'loss_infure_id' => 'nullable|integer|exists:mslossinfure,id',
            'berat'          => 'nullable|numeric|min:0',
            'frekuensi'      => 'nullable|integer|min:0',
            'keterangan'     => 'nullable|string|max:500',
        ]);

        if (isset($validated['loss_infure_id'])) $loss->loss_infure_id = $validated['loss_infure_id'];
        if (isset($validated['berat']))          $loss->berat          = $validated['berat'];
        if (isset($validated['frekuensi']))      $loss->frekuensi      = $validated['frekuensi'];
        if (isset($validated['keterangan']))     $loss->keterangan     = $validated['keterangan'];

        $loss->berat_loss = $loss->berat * $loss->frekuensi;
        $loss->save();

        return ApiResponse::success(null, 'Loss berhasil diupdate.');
    }

    /**
     * DELETE /api/mobile/loss-infure/{id}
     */
    public function destroy($id)
    {
        TdProductAssemblyLoss::findOrFail($id)->delete();

        return ApiResponse::deleted('Loss berhasil dihapus.');
    }
}

