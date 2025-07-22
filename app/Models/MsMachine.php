<?php

namespace App\Models;

use App\Helpers\departmentHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MsMachine extends Model
{
    use HasFactory;
    protected $table = "msmachine";
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

    public function scopeInfureDepartment($query)
    {
        return $query->whereIn('msmachine.department_id', departmentHelper::infureDepartment());
    }

    public function scopeSeitaiDepartment($query)
    {
        return $query->whereIn('msmachine.department_id', departmentHelper::seitaiDepartment());
    }

    // Relations
    public function jamKerjaMesin()
    {
        return $this->hasMany(TdJamKerjaMesin::class, 'machine_id', 'id');
    }
}
