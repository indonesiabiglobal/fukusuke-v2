<?php

namespace App\Helpers;

use App\Models\MsWorkingShift;
use Carbon\Carbon;

class LossInfureHelper
{
    public static function lossClassIdDashboard()
    {
        // Loss Class ID untuk dashboard Infure
        // 3. Kualitas
        // 4. Printing
        // 5. Mesin
        return [3,4,5];
    }
}
