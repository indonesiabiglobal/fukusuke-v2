<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\AddLpkController;
use App\Http\Livewire\EditLpkController;
use App\Http\Livewire\AddOrderController;
use App\Http\Livewire\CetakLpkController;
use App\Http\Livewire\LpkEntryController;
use App\Http\Livewire\OrderLpkController;
use App\Http\Controllers\InfureController;
use App\Http\Livewire\EditOrderController;
use App\Http\Livewire\MasterTabel\Machine\Machine;
use App\Http\Livewire\MasterTabel\Employee;
use App\Http\Livewire\MasterTabel\Warehouse;
use App\Http\Livewire\OrderReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Livewire\MasterTabel\Department;
use App\Http\Controllers\ProductionLossReport;
use App\Http\Livewire\MasterTabel\WorkingShift;
use App\Http\Livewire\Kenpin\EditKenpinInfureController;
use App\Http\Livewire\MasterTabel\BuyerController;
use App\Http\Controllers\DashboardInfureController;
use App\Http\Controllers\DashboardInfureControllerOld;
use App\Http\Controllers\DashboardSeitaiController;
use App\Http\Controllers\DashboardSeitaiControllerOld;
use App\Http\Livewire\Kenpin\KenpinInfureController;
use App\Http\Livewire\Kenpin\KenpinSeitaiController;
use App\Http\Livewire\Kenpin\Report\ReportKenpinController;
use App\Http\Livewire\MasterTabel\Produk\EditProduk;
use App\Http\Livewire\MasterTabel\Produk\TipeProduk;
use App\Http\Livewire\Report\DetailReportController;
use App\Http\Livewire\MasterTabel\KatanukiController;
use App\Http\Livewire\MasterTabel\Produk\JenisProduk;
use App\Http\Livewire\NippoInfure\AddNippoController;
use App\Http\Livewire\Report\GeneralReportController;
use App\Http\Livewire\MasterTabel\Produk\MasterProduk;
use App\Http\Livewire\NippoInfure\EditNippoController;
use App\Http\Livewire\NippoSeitai\AddSeitaiController;
use App\Http\Livewire\Administration\AddUserController;
use App\Http\Livewire\Kenpin\AddKenpinInfureController;
use App\Http\Livewire\Kenpin\AddKenpinSeitaiController;
use App\Http\Livewire\NippoInfure\LossInfureController;
use App\Http\Livewire\NippoSeitai\EditSeitaiController;
use App\Http\Livewire\NippoSeitai\LossSeitaiController;
use App\Http\Livewire\Administration\EditUserController;
use App\Http\Livewire\JamKerja\InfureJamKerjaController;
use App\Http\Livewire\JamKerja\SeitaiJamKerjaController;
use App\Http\Livewire\MasterTabel\Kemasan\BoxController;
use App\Http\Livewire\NippoInfure\LabelGentanController;
use App\Http\Livewire\NippoInfure\NippoInfureController;
use App\Http\Livewire\NippoSeitai\NippoSeitaiController;
use App\Http\Livewire\MasterTabel\Produk\AddMasterProduk;
use App\Http\Livewire\Warehouse\PenarikanPaletController;
use App\Http\Livewire\MasterTabel\Kemasan\GaisoController;
use App\Http\Livewire\MasterTabel\Kemasan\InnerController;
use App\Http\Livewire\MasterTabel\Kemasan\LayerController;
use App\Http\Livewire\NippoSeitai\MutasiIsiPaletController;
use App\Http\Livewire\Kenpin\MutasiIsiPaletKenpinController;
use App\Http\Livewire\NippoInfure\CheckListInfureController;
use App\Http\Livewire\NippoSeitai\CheckListSeitaiController;
use App\Http\Livewire\Report\ProductionLossReportController;
use App\Http\Livewire\Warehouse\PengembalianPaletController;
use App\Http\Livewire\NippoSeitai\LabelMasukGudangController;
use App\Http\Livewire\Kenpin\PrintLabelGudangKenpinController;
use App\Http\Livewire\MasterTabel\Loss\MenuLossInfureController;
use App\Http\Livewire\MasterTabel\Loss\MenuLossSeitaiController;
use App\Http\Livewire\Administration\SecurityManagementController;
use App\Http\Livewire\JamKerja\CheckListJamKerjaController;
use App\Http\Livewire\Kenpin\EditKenpinSeitaiController;
use App\Http\Livewire\MasterTabel\Inventory\BahanBakuController;
use App\Http\Livewire\MasterTabel\Inventory\BarangJadiController;
use App\Http\Livewire\MasterTabel\Inventory\BarangRejectController;
use App\Http\Livewire\MasterTabel\Inventory\MesinPeralatanController;
use App\Http\Livewire\MasterTabel\JadwalMachineController;
use App\Http\Livewire\MasterTabel\Loss\MenuLossKatagoriController;
use App\Http\Livewire\MasterTabel\Loss\MenuLossKenpinController;
use App\Http\Livewire\MasterTabel\Loss\MenuLossKlasifikisasiController;
use App\Http\Livewire\MasterTabel\Inventory\PemasukanBarangController;
use App\Http\Livewire\MasterTabel\Inventory\PengeluaranBarangController;
use App\Http\Livewire\MasterTabel\Inventory\PosisiWipController;
use App\Http\Livewire\MasterTabel\JamMatiMesin\JamMatiMesinInfureController;
use App\Http\Livewire\MasterTabel\JamMatiMesin\JamMatiMesinSeitaiController;
use App\Http\Livewire\MasterTabel\Machine\MachinePartController;
use App\Http\Livewire\MasterTabel\Machine\MachinePartDetailController;
use App\Http\Livewire\MasterTabel\MasalahKenpin\MasalahKenpinInfureController;
use App\Http\Livewire\MasterTabel\MasalahKenpin\MasalahKenpinSeitaiController;
use App\Http\Controllers\PrinterSettingsController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', \App\Http\Livewire\Auth\Login::class)->name('login');
Route::get('/register', \App\Http\Livewire\Auth\Register::class)->name('register');
Route::get('/forget-password', \App\Http\Livewire\Auth\ForgetPassword::class)->name('password.reset');
Route::get('/new-password', \App\Http\Livewire\Auth\NewPassword::class);
Route::get('/logout', [App\Http\Controllers\HomeController::class, 'logout']);
Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);
Route::get('phpinfo', [App\Http\Controllers\HomeController::class, 'phpinfo']);

// Documentation Routes
Route::get('/docs/{file}', function ($file) {
    $filePath = base_path($file . '.md');

    if (!file_exists($filePath)) {
        abort(404, 'Documentation not found');
    }

    $content = file_get_contents($filePath);

    // Parse markdown title
    preg_match('/^#\s+(.+)$/m', $content, $matches);
    $title = $matches[1] ?? 'Documentation';

    return view('markdown-viewer', [
        'content' => $content,
        'title' => $title
    ]);
})->middleware('auth')->where('file', '.*');

Route::group(['middleware' => 'auth'], function () {
    // Printer Settings
    Route::get('/printer-settings', [PrinterSettingsController::class, 'index'])->middleware('auth')->name('printer.settings');

    // Order LPK
    Route::get('/order-lpk', OrderLpkController::class)->name('order-lpk');
    Route::get('/edit-order', EditOrderController::class)->name('edit-order');
    Route::get('/add-order', AddOrderController::class)->name('add-order');

    Route::get('/lpk-entry', LpkEntryController::class)->name('lpk-entry');
    Route::get('/add-lpk', AddLpkController::class)->name('add-lpk');
    Route::get('/edit-lpk', EditLpkController::class)->name('edit-lpk');

    Route::get('/cetak-lpk', CetakLpkController::class)->name('cetak-lpk');
    Route::get('/order-report', OrderReportController::class)->name('order-report');

    // Nipo Infure
    Route::get('/nippo-infure', NippoInfureController::class)->name('nippo-infure');
    Route::get('/edit-nippo/', EditNippoController::class)->name('edit-nippo');
    Route::get('/add-nippo', AddNippoController::class)->name('add-nippo');
    // Route untuk fetch print data
    Route::get('/get-print-data/{produk_asemblyid}', function ($produk_asemblyid) {
        try {
            $data = DB::table('tdproduct_assembly as tpa')
                ->join('tdorderlpk as tod', 'tpa.lpk_id', '=', 'tod.id')
                ->join('msproduct as mp', 'mp.id', '=', 'tod.product_id')
                ->leftJoin('msworkingshift as msw', 'msw.id', '=', 'tpa.work_shift')
                ->join('msmachine as msm', 'msm.id', '=', 'tpa.machine_id')
                ->join('msemployee as mse', 'mse.id', '=', 'tpa.employee_id')
                ->select([
                    DB::raw("COALESCE(tpa.gentan_no, 0) as gentan_no"),
                    DB::raw("COALESCE(tod.lpk_no, '-') as lpk_no"),
                    DB::raw("COALESCE(mp.name, '-') as product_name"),
                    DB::raw("COALESCE(mp.code, '-') as code"),
                    DB::raw("COALESCE(mp.code_alias, '-') as code_alias"),
                    DB::raw("to_char(tpa.production_date, 'DD-MM-YYYY') as production_date"),
                    DB::raw("COALESCE(tpa.work_hour, '00:00') as work_hour"),
                    DB::raw("COALESCE(msw.id::text, '0') as work_shift"),
                    DB::raw("COALESCE(msm.machineno, '-') as machineno"),
                    DB::raw("COALESCE(tpa.berat_produksi, 0) as berat_produksi"),
                    DB::raw("COALESCE(tpa.panjang_produksi, 0) as panjang_produksi"),
                    DB::raw("COALESCE(tod.total_assembly_line - tod.panjang_lpk, 0) as selisih"),
                    DB::raw("COALESCE(tpa.nomor_han, '-') as nomor_han"),
                    DB::raw("COALESCE(mse.employeeno, '-') as nik"),
                    DB::raw("COALESCE(mse.empname, '-') as empname")
                ])
                ->where('tpa.id', $produk_asemblyid)
                ->first();

            if (!$data) {
                Log::error('Data not found for ID: ' . $produk_asemblyid);
                return response()->json(['error' => 'Data not found'], 404);
            }

            Log::info('Print data fetched successfully');
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Get print data error: ' . $e->getMessage());

            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    })->name('get-print-data');

    Route::get('/loss-infure', LossInfureController::class)->name('loss-infure');

    Route::get('/checklist-infure', CheckListInfureController::class)->name('checklist-infure');
    Route::get('/label-gentan', LabelGentanController::class)->name('label-gentan');

    Route::get('/report-checklist-infure', function (Request $request) {
        $lpk_id = '180502-006';
        return view('livewire.nippo-infure.report-check-list', compact('lpk_id'));
    })->name('nippo-infure-print');

    // Nippo Seitai
    Route::get('/nippo-seitai', NippoSeitaiController::class)->name('nippo-seitai');
    Route::get('/add-seitai', AddSeitaiController::class)->name('add-seitai');
    Route::get('/edit-seitai', EditSeitaiController::class)->name('edit-seitai');

    Route::get('/loss-seitai', LossSeitaiController::class)->name('loss-seitai');
    Route::get('/add-loss', AddSeitaiController::class)->name('add-loss');

    Route::get('/mutasi-isi-palet', MutasiIsiPaletController::class)->name('mutasi-isi-palet');
    Route::get('/check-list-seitai', CheckListSeitaiController::class)->name('check-list-seitai');
    Route::get('/label-masuk-gudang', LabelMasukGudangController::class)->name('label-masuk-gudang');

    // Jam Kerja
    Route::get('/infure-jam-kerja', InfureJamKerjaController::class)->name('infure-jam-kerja');
    Route::get('/seitai-jam-kerja', SeitaiJamKerjaController::class)->name('seitai-jam-kerja');
    Route::get('/checklist-jam-kerja', CheckListJamKerjaController::class)->name('checklist-jam-kerja');

    // Kenpin
    Route::get('/kenpin-infure', KenpinInfureController::class)->name('kenpin-infure');
    Route::get('/add-kenpin-infure', AddKenpinInfureController::class)->name('add-kenpin-infure');
    Route::get('/edit-kenpin-infure', EditKenpinInfureController::class)->name('edit-kenpin-infure');

    Route::get('/kenpin-seitai', KenpinSeitaiController::class)->name('kenpin-seitai-kenpin');
    Route::get('/add-kenpin-seitai', AddKenpinSeitaiController::class)->name('add-kenpin-seitai');
    Route::get('/edit-kenpin-seitai', EditKenpinSeitaiController::class)->name('edit-kenpin-seitai');

    Route::get('/mutasi-isi-palet-kenpin', MutasiIsiPaletKenpinController::class)->name('mutasi-isi-palet-kenpin');
    Route::get('/print-label-gudang-kenpin', PrintLabelGudangKenpinController::class)->name('print-label-gudang-kenpin');
    Route::get('/report-kenpin', ReportKenpinController::class)->name('report-kenpin');

    // Warehouse
    Route::get('/penarikan-palet', PenarikanPaletController::class)->name('penarikan-palet');
    Route::get('/pengembalian-palet', PengembalianPaletController::class)->name('pengembalian-palet');

    Route::get('/general-report', GeneralReportController::class)->name('general-report');
    Route::get('/detail-report', DetailReportController::class)->name('detail-report');
    Route::get('/production-loss-report', ProductionLossReportController::class)->name('production-loss-report');

    // Buyer
    Route::get('/buyer', BuyerController::class)->name('buyer');

    // Master Tabel Produk
    Route::get('/tipe-produk', TipeProduk::class)->name('tipe-produk');
    Route::get('/jenis-produk', JenisProduk::class)->name('jenis-produk');

    // master table department
    Route::get('/departemen', Department::class)->name('department');

    // master table working shift
    Route::get('/working-shift', WorkingShift::class)->name('working-shift');

    // master table warehouse
    Route::get('/warehouse', Warehouse::class)->name('warehouse');

    // master table mesin
    Route::get('/mesin', Machine::class)->name('mesin');
    Route::get('/bagian-mesin', MachinePartController::class)->name('bagian-mesin');
    Route::get('/detail-bagian-mesin', MachinePartDetailController::class)->name('detail-bagian-mesin');

    Route::get('/jadwal-mesin', JadwalMachineController::class)->name('jadwal-mesin');

    // master table karyawan
    Route::get('/karyawan', Employee::class)->name('karyawan');

    // master table katanuki
    Route::get('/menu-katanuki', KatanukiController::class)->name('menu-katanuki');

    // master table produk
    Route::get('/master-produk', MasterProduk::class)->name('product');
    Route::get('/add-master-produk', AddMasterProduk::class)->name('add-master-product');
    Route::get('/edit-master-produk', EditProduk::class)->name('edit-master-product');

    Route::get('/menu-loss-infure', MenuLossInfureController::class)->name('menu-loss-infure');
    Route::get('/menu-loss-kategori', MenuLossKatagoriController::class)->name('menu-loss-katagori');
    Route::get('/menu-loss-kenpin', MenuLossKenpinController::class)->name('menu-loss-kenpin');
    Route::get('/menu-loss-klasifikasi', MenuLossKlasifikisasiController::class)->name('menu-loss-klasifikisasi');
    Route::get('/menu-loss-seitai', MenuLossSeitaiController::class)->name('menu-loss-seitai');

    Route::get('/kemasan-box', BoxController::class)->name('kemasan-box');
    Route::get('/kemasan-gasio', GaisoController::class)->name('kemasan-gasio');
    Route::get('/kemasan-inner', InnerController::class)->name('kemasan-inner');
    Route::get('/kemasan-layer', LayerController::class)->name('kemasan-layer');

    Route::get('/jam-mati-mesin-infure', JamMatiMesinInfureController::class)->name('master-jam-mati-mesin-infure');
    Route::get('/jam-mati-mesin-seitai', JamMatiMesinSeitaiController::class)->name('master-jam-mati-mesin-seitai');

    // Masalah Kenpin
    Route::get('/masalah-kenpin-infure', MasalahKenpinInfureController::class)->name('masalah-kenpin-infure');
    Route::get('/masalah-kenpin-seitai', MasalahKenpinSeitaiController::class)->name('masalah-kenpin-seitai');

    // Administration
    Route::get('/security-management', SecurityManagementController::class)->name('security-management');
    Route::get('/add-user', AddUserController::class)->name('add-user');
    Route::get('/edit-user', EditUserController::class)->name('edit-user');
    Route::get('/role-management', \App\Http\Livewire\Administration\RoleManagementController::class)->name('role-management');

    Route::get('/pemasukan-barang', PemasukanBarangController::class)->name('pemasukan-barang');
    Route::get('/pengeluaran-barang', PengeluaranBarangController::class)->name('pengeluaran-barang');
    Route::get('/posisi-wip', PosisiWipController::class)->name('posisi-wip');
    Route::get('/bahan-baku', BahanBakuController::class)->name('bahan-baku');
    Route::get('/barang-jadi', BarangJadiController::class)->name('barang-jadi');
    Route::get('/mesin-peralatan', MesinPeralatanController::class)->name('mesin-peralatan');
    Route::get('/barang-reject', BarangRejectController::class)->name('barang-reject');

    Route::get('/report-lpk', function (Request $request) {
        // $lpk_ids = $request->query('lpk_ids');
        $lpk_ids = explode(',', request('lpk_ids'));
        $placeholders = implode(',', array_fill(0, count($lpk_ids), '?'));
        return view('livewire.order-lpk.report-lpk', compact('lpk_ids', 'placeholders'));
    })->name('report-lpk');

    Route::get('/report-masuk-gudang', function (Request $request) {
        $no_palet = $request->query('no_palet');
        return view('livewire.nippo-seitai.report-masuk-gudang', compact('no_palet'));
    })->name('report-masuk-gudang');

    Route::get('/report-nippo-infure', function (Request $request) {
        $no_palet = $request->query('no_palet');
        return view('livewire.nippo-infure.report-nippo-infure', compact('no_palet'));
    })->name('report-nippo-infure');

    Route::get('/report-gentan', function (Request $request) {
        $produk_asemblyid = $request->query('produk_asemblyid');
        $lpk_no = $request->query('lpk_no');
        $name = $request->query('name');
        $code = $request->query('code');
        $product_type_code = $request->query('product_type_code');
        $production_date = $request->query('production_date');
        $work_hour = $request->query('work_hour');
        $work_shift = $request->query('work_shift');
        $machineno = $request->query('machineno');
        $berat_produksi = $request->query('berat_produksi');
        $nomor_han = $request->query('nomor_han');
        $nik = $request->query('nik');
        $empname = $request->query('empname');
        return view('livewire.nippo-infure.report-gentan', compact('produk_asemblyid', 'lpk_no', 'name', 'code', 'product_type_code', 'production_date', 'work_hour', 'work_shift', 'machineno', 'berat_produksi', 'nomor_han', 'nik', 'empname'));
    })->name('report-gentan');

    Route::get('/report-nippo-infure', function (Request $request) {
        $tanggal = $request->query('tanggal');
        return view('livewire.nippo-infure.report-nippo-infure', compact('tanggal'));
    })->name('report-nippo-infure');

    Route::get('/cetak-order', function (Request $request) {
        $orderId = $request->query('orderId');
        return view('livewire.order-lpk.cetak-order', compact('orderId'));
    })->name('cetak-order');

    Route::get('/report-checklist-seitai', function (Request $request) {
        $tanggal = $request->query('tanggal');
        return view('livewire.nippo-seitai.report-checklist-seitai', compact('tanggal'));
    })->name('report-checklist-seitai');

    Route::get('/report-loss-seitai', function (Request $request) {
        $tanggal = $request->query('tanggal');
        return view('livewire.nippo-seitai.report-loss-seitai', compact('tanggal'));
    })->name('report-loss-seitai');

    Route::get('/report-gentan', function (Request $request) {
        $produk_asemblyid = $request->query('produk_asemblyid');
        $lpk_no = $request->query('lpk_no');
        $name = $request->query('name');
        $code = $request->query('code');
        $product_type_code = $request->query('product_type_code');
        $production_date = $request->query('production_date');
        $work_hour = $request->query('work_hour');
        $work_shift = $request->query('work_shift');
        $machineno = $request->query('machineno');
        $berat_produksi = $request->query('berat_produksi');
        $nomor_han = $request->query('nomor_han');
        $nik = $request->query('nik');
        $empname = $request->query('empname');
        return view('livewire.nippo-infure.report-gentan', compact('produk_asemblyid', 'lpk_no', 'name', 'code', 'product_type_code', 'production_date', 'work_hour', 'work_shift', 'machineno', 'berat_produksi', 'nomor_han', 'nik', 'empname'));
    })->name('report-gentan');

    Route::get('/test', function () {
        return view('widgets');
    });

    Route::get('/', function () {
        $userAccess = auth()->user()->roles->flatMap->access->pluck('code')->unique()->toArray();
        if (in_array('DASHBOARD-SEITAI', $userAccess)) {
            return redirect()->intended('/dashboard-seitai');
        } else {
            return redirect()->intended('/dashboard-infure');
        }
    });

    // Infure
    Route::controller(DashboardInfureControllerOld::class)->group(function () {
        Route::get('/dashboard-infure-old', 'index')->name('dashboard-infure-old');
        Route::get('/dashboard-infure-old/kadou-jikan', 'getkadouJikanInfure')->name('dashboard-infure-kadou-jikan-infure');
        Route::get('/dashboard-infure-old/hasil-produksi', 'getHasilProduksiInfure')->name('dashboard-infure-hasil-produksi-infure');
        Route::get('/dashboard-infure-old/loss/infuregetLossInfure')->name('dashboard-infure-loss-infure');
        Route::get('/dashboard-infure-old/top-loss', 'getTopLossInfure')->name('dashboard-infure-top-loss-infure');
        Route::get('/dashboard-infure-old/counter-trouble', 'getCounterTroubleInfure')->name('dashboard-infure-counter-trouble-infure');
    });
    Route::controller(DashboardInfureController::class)->group(function () {
        Route::get('/dashboard-infure', 'index')->name('dashboard-infure');
        Route::get('/dashboard-infure/produksi-loss-per-mesin', 'getProduksiLossInfure')->name('dashboard-infure-produksi-loss-per-mesin');
        Route::get('/dashboard-infure/top-loss-per-mesin', 'getTopLossByMachineInfure')->name('dashboard-infure-top-loss-per-mesin');
        Route::get('/dashboard-infure/top-loss-per-kasus', 'getTopLossByKasusInfure')->name('dashboard-infure-top-loss-per-kasus');
        Route::get('/dashboard-infure/kadou-jikan-frekuensi-trouble', 'getKadouJikanFrekuensiTrouble')->name('dashboard-infure-kadou-jikan-frekuensi-trouble');
        Route::get('/dashboard-infure/top-mesin-masalah-loss-daily', 'getTopMesinMasalahLossDaily')->name('dashboard-infure-top-mesin-masalah-loss-daily');
        Route::get('/dashboard-infure/ranking-problem-machine-daily', 'getRankingProblemMachineDaily')->name('dashboard-infure-ranking-problem-machine-daily');

        // monthly
        Route::get('/dashboard-infure/total-produksi-per-bulan', 'getTotalProductionMonthly')->name('dashboard-infure-total-produksi-per-bulan');
        Route::get('/dashboard-infure/peringatan-katagae', 'getPeringatanKatagae')->name('dashboard-infure-peringatan-katagae');
        Route::get('/dashboard-infure/loss-per-bulan', 'getLossMonthly')->name('dashboard-infure-loss-per-bulan');
        Route::get('/dashboard-infure/produksi-per-bulan', 'getProductionMonthly')->name('dashboard-infure-produksi-per-bulan');
        Route::get('/dashboard-infure/top-mesin-masalah-loss-monthly', 'getTopMesinMasalahLossMonthly')->name('dashboard-infure-top-mesin-masalah-loss-monthly');
        Route::get('/dashboard-infure/ranking-problem-machine-monthly', 'getRankingProblemMachineMonthly')->name('dashboard-infure-ranking-problem-machine-monthly');
    });

    // Seitai - hanya route untuk view/page
    Route::controller(DashboardSeitaiController::class)->group(function () {
        Route::get('/dashboard-seitai', 'index')->name('dashboard-seitai');
    });

    // Route::controller(DashboardSeitaiControllerOld::class)->group(function () {
    //     Route::get('/dashboard-seitai-old', 'index')->name('dashboard-seitai');
    //     Route::get('/dashboard-seitai-old/kadou-jikan', 'getkadouJikanSeitai')->name('dashboard-seitai-kadou-jikan-seitai');
    //     Route::get('/dashboard-seitai-old/hasil-produksi', 'getHasilProduksiSeitai')->name('dashboard-seitai-hasil-produksi-seitai');
    //     Route::get('/dashboard-seitai-old/loss/seitaigetLossSeitai')->name('dashboard-seitai-loss-seitai');
    //     Route::get('/dashboard-seitai-old/top-loss', 'getTopLossSeitai')->name('dashboard-seitai-top-loss-seitai');
    //     Route::get('/dashboard-seitai-old/counter-trouble', 'getCounterTroubleSeitai')->name('dashboard-seitai-counter-trouble-seitai');
    // });

    Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index']);
});
