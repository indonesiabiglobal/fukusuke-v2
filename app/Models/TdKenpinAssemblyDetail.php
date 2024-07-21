<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TdKenpinAssemblyDetail extends Model
{
    use HasFactory;
    protected $table = "tdkenpin_assembly_detail";
    protected $fillable = [];
    public $timestamps = false;
}