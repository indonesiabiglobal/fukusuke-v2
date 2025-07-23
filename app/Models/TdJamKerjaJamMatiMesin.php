<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TdJamKerjaJamMatiMesin extends Model
{
    use HasFactory;
    protected $table = "tdjamkerja_jammatimesin";
    protected $fillable = [];
    public $timestamps = false;

    // relations
    public function jamMatiMesin()
    {
        return $this->belongsTo(MsJamMatiMesin::class, 'jam_mati_mesin_id', 'id');
    }
}
