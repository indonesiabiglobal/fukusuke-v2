<?php

namespace App\Helpers;

use App\Models\MsDepartment;

class departmentHelper
{
    public static function infureDivision()
    {
        return MsDepartment::where('name', 'INFURE')->first('id');
    }

    public static function infureDepartment()
    {
        return MsDepartment::where('name', 'ilike', '%INFURE%')->get('id');
    }

    public static function infureMachineDepartment()
    {
        return MsDepartment::select('id', 'name', 'code')
            ->where('division_code', '10')->get();
    }

    public static function seitaiDivision()
    {
        return MsDepartment::where('name', 'SEITAI')->first('id');
    }

    public static function seitaiDepartment()
    {
        return MsDepartment::where('name', 'ilike', '%SEITAI%')->get('id');
    }
}
