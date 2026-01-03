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
        $departments = ['infure', 'seitai'];
        $times = ['07:15', '15:15', '23:15'];

        foreach ($departments as $department) {
            $this->processDepartment($schedule, $department, $times);
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    private function processDepartment(Schedule $schedule, string $department, array $times = []): void
    {
        if (empty($times)) {
            $times = [now()->format('H:i')];
        }

        foreach ($times as $time) {
            $timeName = str_replace(':', '', $time);
            $schedule->command("jamkerja:auto-insert --department={$department}")
                ->name("jamkerja-{$department}-{$timeName}")
                ->dailyAt($time)
                ->withoutOverlapping();
        }
    }
}
