<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Web requests: batasi memory dan execution time untuk keamanan
        // Artisan/CLI: tidak dibatasi agar serve/queue/command bisa jalan terus
        if (!app()->runningInConsole()) {
            ini_set('memory_limit', env('PHP_MEMORY_LIMIT', '256M'));
            ini_set('max_execution_time', env('PHP_MAX_EXECUTION_TIME', '300'));
        }

        // Slow query logging: aktifkan hanya di debug mode agar tidak ada
        // overhead I/O di production saat banyak slow query terjadi bersamaan
        if (config('app.debug')) {
            DB::listen(function ($query) {
                if ($query->time > 100) {
                    $isHttp = !app()->runningInConsole();
                    Log::warning('Slow Query Detected', [
                        'sql'        => $query->sql,
                        'bindings'   => $query->bindings,
                        'time_ms'    => $query->time,
                        'connection' => $query->connectionName,
                        'context'    => $isHttp ? 'http' : 'cli',
                        'url'        => $isHttp ? request()->fullUrl() : null,
                        'route'      => $isHttp ? optional(request()->route())->getName() : null,
                    ]);
                }
            });
        }
    }
}
