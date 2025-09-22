<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Auto-insert jam kerja for Infure department
        // Run every 2 hours to catch shift changes
        // $schedule->command('jamkerja:auto-insert --department=infure')
        //         ->everyTwoHours()
        //         ->withoutOverlapping()
        //         ->runInBackground();

        // Auto-insert jam kerja for Seitai department
        // Run every 2 hours to catch shift changes
        // $schedule->command('jamkerja:auto-insert --department=seitai')
        //         ->everyTwoHours()
        //         ->withoutOverlapping()
        //         ->runInBackground();

        // Alternative: Run at specific times that align with shift changes
        // Uncomment and modify the times below based on your shift schedule

        // Run at 7:15 AM (after morning shift starts)
        $schedule->command('jamkerja:auto-insert --department=infure')
                ->dailyAt('07:15')
                ->withoutOverlapping();
        $schedule->command('jamkerja:auto-insert --department=seitai')
                ->dailyAt('07:15')
                ->withoutOverlapping();

        // Run at 3:15 PM (after afternoon shift starts)
        $schedule->command('jamkerja:auto-insert --department=infure')
                ->dailyAt('12:21')
                ->withoutOverlapping();
        $schedule->command('jamkerja:auto-insert --department=seitai')
                ->dailyAt('15:15')
                ->withoutOverlapping();


        // Run at 11:15 PM (after night shift starts)
        $schedule->command('jamkerja:auto-insert --department=infure')
                ->dailyAt('23:15')
                ->withoutOverlapping();
        $schedule->command('jamkerja:auto-insert --department=seitai')
                ->dailyAt('23:15')
                ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
