<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsKlasifikasiSeal extends Model
{
    use HasFactory;
    protected $table = "msklasifikasiseal";
    public $timestamps = false;
    protected $guarded = ['id'];
}
