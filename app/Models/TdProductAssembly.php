<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TdProductAssembly extends Model
{
    use HasFactory;
    protected $table = "tdproduct_assembly";
    protected $fillable = [];
    public $timestamps = false;
}