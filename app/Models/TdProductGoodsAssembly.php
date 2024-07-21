<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TdProductGoodsAssembly extends Model
{
    use HasFactory;
    protected $table = "tdproduct_goods_assembly";
    protected $fillable = [];
    public $timestamps = false;
}