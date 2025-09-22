<?php

namespace App\Models;

use App\Helpers\departmentHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TdJamKerjaMesin extends Model
{
    use HasFactory;
    protected $table = "tdjamkerjamesin";
    protected $fillable = [];
    // public $timestamps = false;

    // custom created and updated
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = Auth::user()->username ?? 'system';
            $model->updated_by = Auth::user()->username ?? 'system';
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::user()->username ?? 'system';
        });
    }

    public function scopeInfureDepartment($query)
    {
        $department = departmentHelper::infureDivision();
        if ($department) {
            return $query->where('department_id', $department->id);
        }
        return $query;
    }

    public function scopeSeitaiDepartment($query)
    {
        $department = departmentHelper::seitaiDivision();
        if ($department) {
            return $query->where('department_id', $department->id);
        }
        return $query;
    }

    // relations
    public function jamMatiMesin()
    {
        return $this->belongsTo(MsJamMatiMesin::class, 'jam_mati_mesin_id', 'id');
    }

    public function machine()
    {
        return $this->belongsTo(MsMachine::class, 'machine_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(MsEmployee::class, 'employee_id', 'id');
    }

    public function workingShift()
    {
        return $this->belongsTo(MsWorkingShift::class, 'work_shift', 'id');
    }
}
