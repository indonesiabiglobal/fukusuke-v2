<?php

use App\Http\Livewire\AddLpkController;
use App\Http\Livewire\NippoInfure\AddNippoController;
use App\Http\Livewire\AddOrderController;
use App\Http\Livewire\Administration\AddUserController;
use App\Http\Livewire\Administration\EditUserController;
use App\Http\Livewire\Administration\SecurityManagementController;
use App\Http\Livewire\CetakLpkController;
use App\Http\Livewire\NippoInfure\CheckListInfureController;
use App\Http\Livewire\EditLpkController;
use Illuminate\Http\Request;
use App\Http\Livewire\EditOrderController;
use App\Http\Livewire\JamKerja\InfureJamKerjaController;
use App\Http\Livewire\JamKerja\SeitaiJamKerjaController;
use App\Http\Livewire\Kenpin\AddKenpinInfureController;
use App\Http\Livewire\Kenpin\AddKenpinSeitaiController;
use App\Http\Livewire\Kenpin\EditKenpinController;
use App\Http\Livewire\Kenpin\KenpinInfureController;
use App\Http\Livewire\Kenpin\KenpinSeitaiController;
use App\Http\Livewire\Kenpin\MutasiIsiPaletKenpinController;
use App\Http\Livewire\Kenpin\PrintLabelGudangKenpinController;
use App\Http\Livewire\Kenpin\ReportKenpinController;
use App\Http\Livewire\LpkEntryController;
use App\Http\Livewire\MasterTabel\BuyerController;
use App\Http\Livewire\MasterTabel\Department;
use App\Http\Livewire\MasterTabel\Employee;
use App\Http\Livewire\MasterTabel\Katanuki;
use App\Http\Livewire\MasterTabel\Loss\MenuLossInfureController;
use App\Http\Livewire\MasterTabel\Loss\MenuLossKatagoriController;
use App\Http\Livewire\MasterTabel\Loss\MenuLossKlasifikisasiController;
use App\Http\Livewire\MasterTabel\Loss\MenuLossSeitaiController;
use App\Http\Livewire\MasterTabel\Machine;
use App\Http\Livewire\MasterTabel\Produk\JenisProduk;
use App\Http\Livewire\MasterTabel\Produk\TipeProduk;
use App\Http\Livewire\MasterTabel\Warehouse;
use App\Http\Livewire\MasterTabel\WorkingShift;
use App\Http\Livewire\NippoInfure\EditNippoController;
use App\Http\Livewire\NippoInfure\LabelGentanController;
use App\Http\Livewire\NippoInfure\LossInfureController;
use App\Http\Livewire\NippoInfure\NippoInfureController;
use App\Http\Livewire\NippoSeitai\AddSeitaiController;
use App\Http\Livewire\NippoSeitai\CheckListSeitaiController;
use App\Http\Livewire\NippoSeitai\EditSeitaiController;
use App\Http\Livewire\NippoSeitai\LossSeitaiController;
use App\Http\Livewire\NippoSeitai\MutasiIsiPaletController;
use App\Http\Livewire\NippoSeitai\NippoSeitaiController;
use App\Http\Livewire\NippoSeitai\LabelMasukGudangController;
use App\Http\Livewire\OrderLpkController;
use App\Http\Livewire\OrderReportController;
use App\Http\Livewire\Report\DetailReportController;
use App\Http\Livewire\Report\GeneralReportController;
use App\Http\Livewire\Warehouse\PenarikanPaletController;
use App\Http\Livewire\Warehouse\PengembalianPaletController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
Route::get('/new-password/{email?}/{token?}', \App\Http\Livewire\Auth\NewPassword::class);
Route::get('/logout', [App\Http\Controllers\HomeController::class, 'logout']);
Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);

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

Route::get('/mutasi-isi-palet-kenpin', MutasiIsiPaletKenpinController::class)->name('mutasi-isi-palet-kenpin');
Route::get('/print-label-gudang-kenpin', PrintLabelGudangKenpinController::class)->name('print-label-gudang-kenpin');
Route::get('/report-kenpin', ReportKenpinController::class)->name('report-kenpin');

// Warehouse
Route::get('/penarikan-palet', PenarikanPaletController::class)->name('penarikan-palet');
Route::get('/pengembalian-palet', PengembalianPaletController::class)->name('pengembalian-palet');

Route::get('/general-report', GeneralReportController::class)->name('general-report');
Route::get('/detail-report', DetailReportController::class)->name('detail-report');

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

// master table karyawan
Route::get('/karyawan', Employee::class)->name('karyawan');

// master table katanuki
Route::get('/katanukiki', Katanuki::class)->name('katanuki');

Route::get('/menu-loss-infure', MenuLossInfureController::class)->name('menu-loss-infure');
Route::get('/menu-loss-kategori', MenuLossKatagoriController::class)->name('menu-loss-katagori');
Route::get('/menu-loss-klasifikasi', MenuLossKlasifikisasiController::class)->name('menu-loss-klasifikisasi');
Route::get('/menu-loss-seitai', MenuLossSeitaiController::class)->name('menu-loss-seitai');

// Administration
Route::get('/security-management', SecurityManagementController::class)->name('security-management');
Route::get('/add-user', AddUserController::class)->name('add-user');
Route::get('/edit-user', EditUserController::class)->name('edit-user');

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
    return view('livewire.nippo-infure.report-gentan', compact('lpk_no','name', 'code', 'product_type_code', 'production_date', 'work_hour', 'work_shift', 'machineno', 'berat_produksi', 'nomor_han', 'nik', 'empname'));
})->name('report-gentan');

// Route::get('/cetak-order', function (Request $request) {
//     $processdate = $request->query('processdate');
//     $po_no = $request->query('po_no');
//     $order_date = $request->query('order_date');
//     $code = $request->query('code');
//     $name = $request->query('name');
//     $dimensi = $request->query('dimensi');
//     $order_qty = $request->query('order_qty');
//     $stufingdate = $request->query('stufingdate');
//     $etddate = $request->query('etddate');
//     $etadate = $request->query('etadate');
//     $namabuyer = $request->query('namabuyer');
//     return view('livewire.order-lpk.cetak-order', compact('processdate','po_no', 'order_date', 'code', 'name', 'dimensi', 'order_qty', 'stufingdate', 'etddate', 'etadate', 'namabuyer'));
// })->name('cetak-order');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/test', function() {
        return view('widgets');
    });
    Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index']);

    Route::get('/', function() {
        return view('index');
    });


});

