<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TdKenpinAssembly extends Model
{
    use HasFactory;
    protected $table = "tdkenpin_assembly";
    protected $fillable = [];
    public $timestamps = false;
}