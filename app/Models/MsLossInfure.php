<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsLossInfure extends Model
{
    use HasFactory;
    protected $table = "mslossinfure";
    public $timestamps = false;
    protected $guarded = ['id'];
}
