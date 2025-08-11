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

    public static function infurePabrikDepartment()
    {
        return MsDepartment::select('id', 'name', 'code')
            ->where('name', 'ilike', '%INFURE PAB%')->get();
    }

    public static function seitaiDivision()
    {
        return MsDepartment::where('name', 'SEITAI')->first('id');
    }

    public static function seitaiDepartment()
    {
        return MsDepartment::where('name', 'ilike', '%SEITAI%')->get('id');
    }

    public static function seitaiPabrikDepartment()
    {
        return MsDepartment::select('id', 'name', 'code')
            ->where('name', 'ilike', '%SEITAI PAB%')->get();
    }

    public static function masalahKenpinSeitaiDepartment()
    {
        return MsDepartment::select('id', 'name', 'code')
            ->whereIn('id', [2, 7])->get();
    }
}
