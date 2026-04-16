<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Models\TdProductAssembly;
use App\Models\TdProductAssemblyLoss;
use App\Models\TdOrderLpk;
use App\Models\MsMachine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MobileNippoInfureController extends Controller
{
    /**
     * GET /api/mobile/nippo-infure
     * Query params: tglMasuk, tglKeluar, transaksi, lpk_no, status, searchTerm
     */
    public function index(Request $request)
    {
        $tglMasuk  = $request->get('tglMasuk',  Carbon::today()->format('Y-m-d'));
        $tglKeluar = $request->get('tglKeluar', Carbon::today()->format('Y-m-d'));
        $transaksi = $request->get('transaksi', 1);
        $lpk_no    = $request->get('lpk_no',    '');
        $status    = $request->get('status',    '');
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
                'tdol.qty_gulung AS qty_gulung',
                'tdol.total_assembly_line AS total_assembly_line',
                'tdol.total_assembly_qty AS total_assembly_qty',
                'msm.machineno',
                'msm.machinename',
                'tdo.product_code',
                'mp.name AS product_name',
            ])
            ->join('tdorderlpk AS tdol', 'tda.lpk_id', '=', 'tdol.id')
            ->leftJoin('msproduct AS mp', 'mp.id', '=', 'tdol.product_id')
            ->join('msmachine AS msm', 'msm.id', '=', 'tda.machine_id')
            ->join('tdorder AS tdo', 'tdol.order_id', '=', 'tdo.id');

        // Date filter — same logic as NippoInfureController::render()
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

        // LPK No filter — partial match, same as Livewire (strlen == 10)
        if ($lpk_no && strlen($lpk_no) == 10) {
            $query->where('tdol.lpk_no', 'ilike', "%{$lpk_no}%");
        }

        // Status filter — same 3-value logic as Livewire
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

        // Full-text search — same fields as Livewire
        if ($search && $search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('tda.production_no', 'ilike', "%{$search}%")
                  ->orWhere('mp.name', 'ilike', "%{$search}%")
                  ->orWhere('mp.code', 'ilike', "%{$search}%")
                  ->orWhere('tda.machine_id', 'ilike', "%{$search}%")
                  ->orWhere('tda.nomor_han', 'ilike', "%{$search}%");
            });
        }

        $perPage = min((int) $request->get('per_page', 20), 100);

        $paginator = $query->orderBy('tda.created_on', 'desc')->paginate($perPage);

        return ApiResponse::paginated($paginator);
    }

    /**
     * GET /api/mobile/nippo-infure/{id}
     */
    public function show($id)
    {
        $data = DB::table('tdproduct_assembly AS tda')
            ->select([
                'tda.*',
                DB::raw('CASE WHEN tda.berat_standard = 0 THEN 0 ELSE (tda.berat_produksi / tda.berat_standard * 100) END AS rasio_produksi'),
                DB::raw('tdol.total_assembly_line - tdol.panjang_lpk AS selisih'),
                'tdol.lpk_no',
                'tdol.lpk_date',
                'tdol.panjang_lpk',
                'tdol.qty_gentan',
                'tdol.qty_gulung',
                'tdol.total_assembly_line',
                'tdol.total_assembly_qty',
                'msm.machineno',
                'msm.machinename',
                'tdo.product_code',
                'mp.name AS product_name',
            ])
            ->join('tdorderlpk AS tdol', 'tda.lpk_id', '=', 'tdol.id')
            ->leftJoin('msproduct AS mp', 'mp.id', '=', 'tdol.product_id')
            ->join('msmachine AS msm', 'msm.id', '=', 'tda.machine_id')
            ->join('tdorder AS tdo', 'tdol.order_id', '=', 'tdo.id')
            ->where('tda.id', $id)
            ->first();

        if (! $data) {
            return ApiResponse::notFound();
        }

        // Attach loss details
        $data->loss_details = DB::table('tdproduct_assembly_loss AS tal')
            ->select('tal.*', 'msi.code AS loss_infure_code', 'msi.name AS name_infure')
            ->join('mslossinfure AS msi', 'msi.id', '=', 'tal.loss_infure_id')
            ->where('tal.product_assembly_id', $id)
            ->orderBy('tal.id')
            ->get();

        return ApiResponse::success($data);
    }

    /**
     * POST /api/mobile/nippo-infure
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'production_date'  => 'required|date',
            'lpk_no'           => 'required|string|size:10',
            'machine_id'       => 'required|integer|exists:msmachine,id',
            'work_shift'       => 'required|string|max:2',
            'work_hour'        => 'nullable|string|max:10',
            'panjang_produksi' => 'nullable|numeric|min:0',
            'berat_produksi'   => 'nullable|numeric|min:0',
            'nomor_han'        => 'nullable|string|max:50',
            'gentan_no'        => 'nullable|string|max:20',
            'details'          => 'nullable|array',
        ]);

        // Resolve lpk_id
        $lpk = TdOrderLpk::where('lpk_no', $validated['lpk_no'])->first();
        if (! $lpk) {
            return response()->json(['message' => 'LPK tidak ditemukan.'], 422);
        }

        // Build production_no: INFURE + date + seq (same pattern as web)
        $date  = Carbon::parse($validated['production_date']);
        $today = $date->format('ymd');
        $seq   = DB::table('tdproduct_assembly')
            ->whereDate('production_date', $date->format('Y-m-d'))
            ->count() + 1;
        $production_no = 'INFURE' . $today . str_pad($seq, 3, '0', STR_PAD_LEFT);

        $assembly = new TdProductAssembly();
        $assembly->production_no     = $production_no;
        $assembly->production_date   = $date->format('Y-m-d');
        $assembly->employee_id       = Auth::user()->employee_id ?? 1;
        $assembly->work_shift        = $validated['work_shift'];
        $assembly->work_hour         = $validated['work_hour'] ?? '';
        $assembly->machine_id        = $validated['machine_id'];
        $assembly->lpk_id            = $lpk->id;
        $assembly->product_id        = $lpk->product_id;
        $assembly->panjang_produksi  = $validated['panjang_produksi'] ?? 0;
        $assembly->berat_produksi    = $validated['berat_produksi'] ?? 0;
        $assembly->nomor_han         = $validated['nomor_han'] ?? '';
        $assembly->gentan_no         = $validated['gentan_no'] ?? '';
        $assembly->status_production = 0;
        $assembly->save();

        // Save loss details
        if (! empty($validated['details'])) {
            foreach ($validated['details'] as $d) {
                if (empty($d['loss_infure_id'])) continue;

                $loss = new TdProductAssemblyLoss();
                $loss->product_assembly_id = $assembly->id;
                $loss->loss_infure_id      = $d['loss_infure_id'];
                $loss->berat               = $d['berat'] ?? 0;
                $loss->frekuensi           = $d['frekuensi'] ?? 0;
                $loss->berat_loss          = ($d['berat'] ?? 0) * ($d['frekuensi'] ?? 0);
                $loss->save();
            }
        }

        return ApiResponse::created(['id' => $assembly->id]);
    }

    /**
     * PUT /api/mobile/nippo-infure/{id}
     */
    public function update(Request $request, $id)
    {
        $assembly = TdProductAssembly::findOrFail($id);

        $validated = $request->validate([
            'production_date'  => 'nullable|date',
            'machine_id'       => 'nullable|integer|exists:msmachine,id',
            'work_shift'       => 'nullable|string|max:2',
            'work_hour'        => 'nullable|string|max:10',
            'panjang_produksi' => 'nullable|numeric|min:0',
            'berat_produksi'   => 'nullable|numeric|min:0',
            'nomor_han'        => 'nullable|string|max:50',
            'gentan_no'        => 'nullable|string|max:20',
        ]);

        $assembly->fill(array_filter($validated, fn($v) => $v !== null));
        $assembly->save();

        return ApiResponse::success(null, 'Data berhasil diupdate.');
    }

    /**
     * DELETE /api/mobile/nippo-infure/{id}
     */
    public function destroy($id)
    {
        $assembly = TdProductAssembly::findOrFail($id);
        TdProductAssemblyLoss::where('product_assembly_id', $id)->delete();
        $assembly->delete();

        return ApiResponse::deleted();
    }
}

