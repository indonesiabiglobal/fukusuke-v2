<?php

namespace App\Models;

use App\Helpers\departmentHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MsJamMatiMesin extends Model
{
    use HasFactory;
    protected $table = "ms_jam_mati_mesin";
    protected $fillable = [];
    protected $guarded = [];

    public function scopeInfureDivision($query)
    {
        return $query->whereIn('ms_jam_mati_mesin.department_id', departmentHelper::infureDivisiom());
    }

    public function scopeSeitaiDivision($query)
    {
        return $query->whereIn('ms_jam_mati_mesin.department_id', departmentHelper::seitaiDivisiom());
    }
}
