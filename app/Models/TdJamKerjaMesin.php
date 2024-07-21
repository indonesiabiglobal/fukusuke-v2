<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TdJamKerjaMesin extends Model
{
    use HasFactory;
    protected $table = "tdjamkerjamesin";
    protected $fillable = [];
    public $timestamps = false;
}