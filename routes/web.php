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
use App\Http\Livewire\MasterTabel\Machine;
use App\Http\Livewire\MasterTabel\Employee;
use App\Http\Livewire\MasterTabel\Warehouse;
use App\Http\Livewire\OrderReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Livewire\MasterTabel\Department;
use App\Http\Controllers\ProductionLossReport;
use App\Http\Livewire\MasterTabel\WorkingShift;
use App\Http\Livewire\Kenpin\EditKenpinController;
use App\Http\Livewire\MasterTabel\BuyerController;
use App\Http\Controllers\DashboardInfureController;
use App\Http\Controllers\DashboardSeitaiController;
use App\Http\Livewire\Kenpin\KenpinInfureController;
use App\Http\Livewire\Kenpin\KenpinSeitaiController;
use App\Http\Livewire\Kenpin\ReportKenpinController;
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

Route::group(['middleware' => 'auth'], function () {
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

    // Kenpin
    Route::get('/kenpin-infure', KenpinInfureController::class)->name('kenpin-infure');
    Route::get('/add-kenpin-infure', AddKenpinInfureController::class)->name('add-kenpin-infure');
    Route::get('/edit-kenpin-infure', EditKenpinController::class)->name('edit-kenpin-infure');

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

    // Administration
    Route::get('/security-management', SecurityManagementController::class)->name('security-management');
    Route::get('/add-user', AddUserController::class)->name('add-user');
    Route::get('/edit-user', EditUserController::class)->name('edit-user');

    Route::get('/pemasukan-barang', PemasukanBarangController::class)->name('pemasukan-barang');
    Route::get('/pengeluaran-barang', PengeluaranBarangController::class)->name('pengeluaran-barang');
    Route::get('/posisi-wip', PosisiWipController::class)->name('posisi-wip');
    Route::get('/bahan-baku', BahanBakuController::class)->name('bahan-baku');
    Route::get('/barang-jadi', BarangJadiController::class)->name('barang-jadi');
    Route::get('/mesin-peralatan', MesinPeralatanController::class)->name('mesin-peralatan');
    Route::get('/barang-reject', BarangRejectController::class)->name('barang-reject');

    Route::get('/report-lpk', function (Request $request) {
        $lpk_id = $request->query('lpk_id');
        return view('livewire.order-lpk.report-lpk', compact('lpk_id'));
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
        return view('livewire.nippo-infure.report-gentan', compact('lpk_no', 'name', 'code', 'product_type_code', 'production_date', 'work_hour', 'work_shift', 'machineno', 'berat_produksi', 'nomor_han', 'nik', 'empname'));
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
        return view('livewire.nippo-infure.report-gentan', compact('lpk_no', 'name', 'code', 'product_type_code', 'production_date', 'work_hour', 'work_shift', 'machineno', 'berat_produksi', 'nomor_han', 'nik', 'empname'));
    })->name('report-gentan');

    Route::get('/test', function () {
        return view('widgets');
    });

    // Route::get('/', function() {
    //     return view('index');
    // });

    Route::controller(DashboardController::class)->group(function () {
        Route::get('/', 'index')->name('dashboard');
        Route::get('/dashboard-ppic', 'ppic')->name('dashboard-ppic');
        Route::get('/dashboard-qc', 'qc')->name('dashboard-qc');
        // Infure
        Route::get('/kadou-jikan/infure', 'getkadouJikanInfure')->name('kadou-jikan-infure');
        Route::get('/hasil-produksi/infure', 'getHasilProduksiInfure')->name('hasil-produksi-infure');
        Route::get('/loss/infure', 'getLossInfure')->name('loss-infure');
        Route::get('/top-loss/infure', 'getTopLossInfure')->name('top-loss-infure');
        Route::get('/counter-trouble/infure', 'getCounterTroubleInfure')->name('counter-trouble-infure');
        // Seitai
        Route::get('/kadou-jikan/seitai', 'getkadouJikanSeitai')->name('kadou-jikan-seitai');
        Route::get('/hasil-produksi/seitai', 'getHasilProduksiSeitai')->name('hasil-produksi-seitai');
        Route::get('/loss/seitai', 'getLossSeitai')->name('loss-seitai');
        Route::get('/top-loss/seitai', 'getTopLossSeitai')->name('top-loss-seitai');
        Route::get('/counter-trouble/seitai', 'getCounterTroubleSeitai')->name('counter-trouble-seitai');
    });

    // Infure
    Route::controller(DashboardInfureController::class)->group(function () {
        Route::get('/dashboard-infure', 'index')->name('dashboard-infure');
        Route::get('/dashboard-infure/kadou-jikan', 'getkadouJikanInfure')->name('dashboard-infure-kadou-jikan-infure');
        Route::get('/dashboard-infure/hasil-produksi', 'getHasilProduksiInfure')->name('dashboard-infure-hasil-produksi-infure');
        Route::get('/dashboard-infure/loss/infuregetLossInfure')->name('dashboard-infure-loss-infure');
        Route::get('/dashboard-infure/top-loss', 'getTopLossInfure')->name('dashboard-infure-top-loss-infure');
        Route::get('/dashboard-infure/counter-trouble', 'getCounterTroubleInfure')->name('dashboard-infure-counter-trouble-infure');
    });

    // Seitai
    Route::controller(DashboardSeitaiController::class)->group(function () {
        Route::get('/dashboard-seitai', 'index')->name('dashboard-seitai');
        Route::get('/dashboard-seitai/kadou-jikan', 'getkadouJikanSeitai')->name('dashboard-seitai-kadou-jikan-seitai');
        Route::get('/dashboard-seitai/hasil-produksi', 'getHasilProduksiSeitai')->name('dashboard-seitai-hasil-produksi-seitai');
        Route::get('/dashboard-seitai/loss/seitaigetLossSeitai')->name('dashboard-seitai-loss-seitai');
        Route::get('/dashboard-seitai/top-loss', 'getTopLossSeitai')->name('dashboard-seitai-top-loss-seitai');
        Route::get('/dashboard-seitai/counter-trouble', 'getCounterTroubleSeitai')->name('dashboard-seitai-counter-trouble-seitai');
    });

    Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index']);


});
