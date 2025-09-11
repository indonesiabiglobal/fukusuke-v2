<?php

namespace App\Models;

use App\Helpers\departmentHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MsMachinePart extends Model
{
    use HasFactory;
    protected $table = "ms_machine_part";
    protected $fillable = [];

    // custom created and updated
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = Auth::user()->username;
            $model->updated_by = Auth::user()->username;
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::user()->username;
        });
    }

    public function scopeInfureDivision($query)
    {
        return $query->whereIn('ms_machine_part.department_id', departmentHelper::infureDivision());
    }

    public function scopeSeitaiDivision($query)
    {
        return $query->whereIn('ms_machine_part.department_id', departmentHelper::seitaiDivision());
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // relations
    public function department()
    {
        return $this->belongsTo(MsDepartment::class, 'department_id');
    }
}
