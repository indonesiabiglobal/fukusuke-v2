<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsLakbanInfure extends Model
{
    use HasFactory;
    protected $table = "mslakbaninfure";
    public $timestamps = false;
    protected $guarded = ['id'];
}
