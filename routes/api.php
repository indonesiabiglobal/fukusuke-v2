<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestTimbanganController;
use App\Http\Controllers\DashboardSeitaiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Test Timbangan API
// Route::post('/test-timbangan', [TestTimbanganController::class, 'store']);

// Sanctum SPA Authentication untuk dashboard
Route::middleware(['web', 'auth'])->group(function () {
    // Seitai Dashboard API
    Route::controller(DashboardSeitaiController::class)->prefix('dashboard-seitai')->group(function () {
        // Daily endpoints
        Route::get('/produksi-loss-per-mesin', 'getProduksiLossSeitai')->name('api.dashboard-seitai-produksi-loss-per-mesin');
        Route::get('/top-loss-per-mesin', 'getTopLossByMachineSeitai')->name('api.dashboard-seitai-top-loss-per-mesin');
        Route::get('/top-loss-per-kasus', 'getTopLossByKasusSeitai')->name('api.dashboard-seitai-top-loss-per-kasus');
        Route::get('/kadou-jikan-frekuensi-trouble', 'getKadouJikanFrekuensiTrouble')->name('api.dashboard-seitai-kadou-jikan-frekuensi-trouble');
        Route::get('/top-mesin-masalah-loss-daily', 'getTopMesinMasalahLossDaily')->name('api.dashboard-seitai-top-mesin-masalah-loss-daily');
        Route::get('/ranking-problem-machine-daily', 'getRankingProblemMachineDaily')->name('api.dashboard-seitai-ranking-problem-machine-daily');

        // Monthly endpoints
        Route::get('/total-produksi-per-bulan', 'getTotalProductionMonthly')->name('api.dashboard-seitai-total-produksi-per-bulan');
        Route::get('/peringatan-katagae', 'getPeringatanKatagae')->name('api.dashboard-seitai-peringatan-katagae');
        Route::get('/loss-per-bulan', 'getLossMonthly')->name('api.dashboard-seitai-loss-per-bulan');
        Route::get('/produksi-per-bulan', 'getProductionMonthly')->name('api.dashboard-seitai-produksi-per-bulan');
        Route::get('/top-mesin-masalah-loss-monthly', 'getTopMesinMasalahLossMonthly')->name('api.dashboard-seitai-top-mesin-masalah-loss-monthly');
        Route::get('/top-loss-per-kasus-monthly', 'getTopLossByCaseMonthly')->name('api.dashboard-seitai-top-loss-per-kasus-monthly');
        Route::get('/ranking-problem-machine-monthly', 'getRankingProblemMachineMonthly')->name('api.dashboard-seitai-ranking-problem-machine-monthly');
    });
});
