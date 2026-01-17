<?php

namespace App\Helpers;

use App\Models\MsDepartment;
use App\Models\MsMachine;

class MachineHelper
{
    public static function getInfureMachine()
    {
        $infureDepartments = departmentHelper::infurePabrikDepartment();
        return MsMachine::whereIN('department_id', $infureDepartments->pluck('id'))->get();
    }

    public static function getSeitaiMachine()
    {
        $seitaiDepartments = departmentHelper::seitaiPabrikDepartment();
        return MsMachine::whereIN('department_id', $seitaiDepartments->pluck('id'))->get();
    }

    public static function getMachineByDepartment($departmentId)
    {
        return MsMachine::where('department_id', $departmentId)->get();
    }
}
