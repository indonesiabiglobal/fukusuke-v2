<?php

namespace App\Console\Commands;

use App\Helpers\departmentHelper;
use App\Models\MsMachine;
use App\Models\MsWorkingShift;
use App\Models\TdJamKerjaMesin;
use App\Models\TdJamKerjaJamMatiMesin;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoInsertJamKerja extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jamkerja:auto-insert {--department=infure} {--dry-run : Show what would be inserted without actually inserting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically insert work hour data for machines in the previous shift';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting auto-insert work hour data process...');

        $department = strtolower($this->option('department'));
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No data will be inserted');
        }

        try {
            // Get current time and determine previous shift
            $currentTime = Carbon::now();
            $previousShift = $this->getPreviousShift($currentTime);

            if (!$previousShift) {
                $this->error('Unable to determine previous shift.');
                return 1;
            }

            $this->info("Processing for previous shift: {$previousShift->work_shift} ({$previousShift->work_hour_from} - {$previousShift->work_hour_till})");

            // Get working date for previous shift
            $workingDate = $this->getWorkingDateForShift($currentTime, $previousShift);

            $this->info("Working date: {$workingDate->format('Y-m-d')}");

            // Get machines based on department
            $machines = $this->getMachinesByDepartment($department);

            if ($machines->isEmpty()) {
                $this->warn("No machines found for department: {$department}");
                return 0;
            }

            $this->info("Found {$machines->count()} machines to process.");

            $insertedCount = 0;
            $skippedCount = 0;

            if (!$isDryRun) {
                DB::beginTransaction();
            }

            foreach ($machines as $machine) {
                // Check if data already exists for this machine, date, and shift
                $exists = TdJamKerjaMesin::where('machine_id', $machine->id)
                    ->where('working_date', $workingDate->format('Y-m-d'))
                    ->where('work_shift', $previousShift->work_shift)
                    ->exists();

                if ($exists) {
                    $skippedCount++;
                    $this->line("Skipped machine {$machine->machineno} - data already exists");
                    continue;
                }

                if ($isDryRun) {
                    $insertedCount++;
                    $this->line("Would insert data for machine: {$machine->machineno}");
                    continue;
                }

                // Insert jam kerja mesin
                $jamKerjaMesin = new TdJamKerjaMesin();
                $jamKerjaMesin->working_date = $workingDate->format('Y-m-d');
                $jamKerjaMesin->work_shift = $previousShift->work_shift;
                $jamKerjaMesin->machine_id = $machine->id;
                $jamKerjaMesin->employee_id = null;
                $jamKerjaMesin->department_id = $this->getDepartmentId($department);
                $jamKerjaMesin->work_hour = '00:00:00';
                $jamKerjaMesin->off_hour = '08:00:00';
                $jamKerjaMesin->on_hour = '00:00:00';
                $jamKerjaMesin->created_on = $currentTime;
                $jamKerjaMesin->created_by = 'system';
                $jamKerjaMesin->updated_on = $currentTime;
                $jamKerjaMesin->updated_by = 'system';

                $jamKerjaMesin->save();

                // Insert jam mati mesin with id 10 and off_hour 08:00
                if ($department === 'infure') {
                    $jamMatiMesinId = 10; // Infure specific jam mati mesin
                } elseif ($department === 'seitai') {
                    $jamMatiMesinId = 17; // Seitai specific jam mati mesin
                }
                TdJamKerjaJamMatiMesin::create([
                    'jam_kerja_mesin_id' => $jamKerjaMesin->id,
                    'jam_mati_mesin_id' => $jamMatiMesinId,
                    'off_hour' => '08:00',
                    'from' => null,
                    'to' => null,
                ]);

                $insertedCount++;
                $this->line("Inserted data for machine: {$machine->machineno}");
            }

            if (!$isDryRun) {
                DB::commit();
            }

            $this->info("Process completed successfully!");
            if ($isDryRun) {
                $this->info("Would insert: {$insertedCount} records");
            } else {
                $this->info("Inserted: {$insertedCount} records");
            }
            $this->info("Skipped: {$skippedCount} records");

            // Log the activity
            if (!$isDryRun) {
                Log::info("Auto-insert jam kerja completed", [
                    'department' => $department,
                    'shift' => $previousShift->work_shift,
                    'working_date' => $workingDate->format('Y-m-d'),
                    'inserted' => $insertedCount,
                    'skipped' => $skippedCount
                ]);
            }

        } catch (\Exception $e) {
            if (!$isDryRun) {
                DB::rollBack();
            }
            $this->error("Error occurred: " . $e->getMessage());
            Log::error("Auto-insert jam kerja failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        return 0;
    }

    /**
     * Get the previous shift based on current time
     */
    private function getPreviousShift(Carbon $currentTime)
    {
        $currentTimeFormatted = $currentTime->format('H:i:s');

        // Get all active shifts ordered by work_hour_from
        $shifts = MsWorkingShift::active()
            ->orderBy('work_hour_from')
            ->get();

        if ($shifts->isEmpty()) {
            return null;
        }

        $currentShift = null;

        // Find current shift
        foreach ($shifts as $shift) {
            if ($this->isTimeInShift($currentTimeFormatted, $shift)) {
                $currentShift = $shift;
                break;
            }
        }

        if (!$currentShift) {
            // If no current shift found, assume we're in the first shift of the day
            $currentShift = $shifts->first();
        }

        // Find previous shift
        $currentIndex = $shifts->search(function($item) use ($currentShift) {
            return $item->id === $currentShift->id;
        });

        if ($currentIndex === false) {
            return null;
        }

        // Get previous shift (if current is first shift, get last shift)
        if ($currentIndex === 0) {
            return $shifts->last();
        } else {
            return $shifts[$currentIndex - 1];
        }
    }

    /**
     * Check if time is within shift hours
     */
    private function isTimeInShift($time, $shift)
    {
        $from = $shift->work_hour_from;
        $till = $shift->work_hour_till;

        if ($from <= $till) {
            // Normal shift (doesn't cross midnight)
            return $time >= $from && $time <= $till;
        } else {
            // Night shift (crosses midnight)
            return $time >= $from || $time <= $till;
        }
    }

    /**
     * Get working date for the shift
     */
    private function getWorkingDateForShift(Carbon $currentTime, $shift)
    {
        // If previous shift crosses midnight and current time is in early morning
        // the working date should be yesterday
        if ($shift->work_hour_from > $shift->work_hour_till && $currentTime->format('H:i:s') <= $shift->work_hour_till) {
            return $currentTime->copy()->subDay();
        }

        // For normal shifts or if we're past the shift end time
        return $currentTime->copy()->subDay();
    }

    /**
     * Get machines by department
     */
    private function getMachinesByDepartment($department)
    {
        switch (strtolower($department)) {
            case 'infure':
                return MsMachine::whereIn('department_id', departmentHelper::infurePabrikDepartment()->pluck('id'))
                    ->where('status', 1)
                    ->get();
            case 'seitai':
                return MsMachine::whereIn('department_id', departmentHelper::seitaiDepartment())
                    ->where('status', 1)
                    ->get();
            default:
                return collect();
        }
    }

    /**
     * Get department ID
     */
    private function getDepartmentId($department)
    {
        switch (strtolower($department)) {
            case 'infure':
                return departmentHelper::infureDivision()->id;
            case 'seitai':
                return departmentHelper::seitaiDivision()->id;
            default:
                return null;
        }
    }
}
