<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TdProductAssemblyLoss extends Model
{
    use HasFactory;
    protected $table = "tdproduct_assembly_loss";
    protected $fillable = [];
    public $timestamps = false;
}