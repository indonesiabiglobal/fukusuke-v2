<?php

use App\Http\Livewire\AddLpkController;
use App\Http\Livewire\NippoInfure\AddNippoController;
use App\Http\Livewire\AddOrderController;
use App\Http\Livewire\CetakLpkController;
use App\Http\Livewire\NippoInfure\CheckListInfureController;
use App\Http\Livewire\EditLpkController;
use Illuminate\Http\Request;
use App\Http\Livewire\EditOrderController;

use App\Http\Livewire\LpkEntryController;
use App\Http\Livewire\NippoInfure\EditNippoController;
use App\Http\Livewire\NippoInfure\LabelGentanController;
use App\Http\Livewire\NippoInfure\LossInfureController;
use App\Http\Livewire\NippoInfure\NippoInfureController;
use App\Http\Livewire\NippoSeitai\AddSeitaiController;
use App\Http\Livewire\NippoSeitai\CheckListSeitai;
use App\Http\Livewire\NippoSeitai\CheckListSeitaiController;
use App\Http\Livewire\NippoSeitai\EditSeitaiController;
use App\Http\Livewire\NippoSeitai\LossSeitaiController;
use App\Http\Livewire\NippoSeitai\MutasiIsiPaletController;
use App\Http\Livewire\NippoSeitai\NippoSeitaiController;
use App\Http\Livewire\NippoSeitai\LabelMasukGudangController;
use App\Http\Livewire\OrderLpkController;
use App\Http\Livewire\OrderReportController;
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

// Nippo Seitai
Route::get('/nippo-seitai', NippoSeitaiController::class)->name('nippo-seitai');
Route::get('/add-seitai', AddSeitaiController::class)->name('add-seitai');
Route::get('/edit-seitai', EditSeitaiController::class)->name('edit-seitai');

Route::get('/loss-seitai', LossSeitaiController::class)->name('loss-seitai');
Route::get('/add-loss', AddSeitaiController::class)->name('add-loss');

Route::get('/mutasi-isi-palet', MutasiIsiPaletController::class)->name('mutasi-isi-palet');
Route::get('/check-list-seitai', CheckListSeitaiController::class)->name('check-list-seitai');
Route::get('/label-masuk-gudang', LabelMasukGudangController::class)->name('label-masuk-gudang');

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

