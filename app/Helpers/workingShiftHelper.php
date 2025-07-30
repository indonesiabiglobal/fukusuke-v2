<?php

namespace App\Helpers;

use App\Models\MsWorkingShift;
use Carbon\Carbon;

class workingShiftHelper
{
    public static function dailtShift($startDate, $endDate)
    {
        $startDate = Carbon::parse($startDate . '07:01:00');
        $endDate = Carbon::parse($endDate . '07:00:00');

        return [$startDate, $endDate];
    }
}
