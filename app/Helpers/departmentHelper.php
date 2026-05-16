<?php

namespace App\Helpers;

use App\Models\MsDepartment;
use App\Models\MsDepartmentGroup;

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
            ->active()
            ->where('name', 'ilike', '%INFURE PAB%')
            ->get();
    }

    public static function masalahKenpinInfureDepartmentGroup()
    {
        return MsDepartmentGroup::select('id', 'name', 'code')
            ->where('department_id', [2])->get();
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
            ->active()
            ->where('name', 'ilike', '%SEITAI PAB%')
            ->get();
    }

    public static function masalahKenpinSeitaiDepartmentGroup()
    {
        return MsDepartmentGroup::select('id', 'name', 'code')
            ->where('department_id', [7])->get();
    }
}
