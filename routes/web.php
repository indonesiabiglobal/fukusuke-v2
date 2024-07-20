<?php

use Illuminate\Http\Request;
use App\Http\Livewire\EditOrderController;
use App\Http\Livewire\OrderLpkController;
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


Route::get('/order-lpk', OrderLpkController::class)->name('order-lpk');
Route::get('/edit-order', EditOrderController::class)->name('edit-order');

Route::get('/cetak-order', function (Request $request) {
    $processdate = $request->query('processdate');
    $po_no = $request->query('po_no');
    $order_date = $request->query('order_date');
    $code = $request->query('code');
    $name = $request->query('name');
    $dimensi = $request->query('dimensi');
    $order_qty = $request->query('order_qty');
    $stufingdate = $request->query('stufingdate');
    $etddate = $request->query('etddate');
    $etadate = $request->query('etadate');
    $namabuyer = $request->query('namabuyer');
    return view('livewire.order-lpk.cetak-order', compact('processdate','po_no', 'order_date', 'code', 'name', 'dimensi', 'order_qty', 'stufingdate', 'etddate', 'etadate', 'namabuyer'));
})->name('cetak-order');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/test', function() {
        return view('widgets');
    });
    Route::get('{any}', [App\Http\Controllers\HomeController::class, 'index']);
    
    Route::get('/', function() {
        return view('index');
    });

    
});

