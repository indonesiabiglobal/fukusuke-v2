<?php

namespace App\Models;

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
            $model->created_by = Auth::user()->username;
            $model->updated_by = Auth::user()->username;
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::user()->username;
        });
    }

    // relations
    public function jamMatiMesin()
    {
        return $this->belongsTo(MsJamMatiMesin::class, 'jam_mati_mesin_id', 'id');
    }
}
