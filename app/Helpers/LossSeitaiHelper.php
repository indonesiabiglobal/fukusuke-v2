<?php

namespace App\Helpers;

use App\Models\MsWorkingShift;
use Carbon\Carbon;

class LossSeitaiHelper
{
    public static function lossClassIdDashboard()
    {
        // Loss Class ID untuk dashboard Seitai
        // 10. Gentan Kake
        // 11. Mesin Utama
        // 12. Katanuki - BU
        // 13. PS
        return [10,11,12,13];
    }
}
