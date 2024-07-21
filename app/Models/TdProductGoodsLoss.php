<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TdProductGoodsLoss extends Model
{
    use HasFactory;
    protected $table = "tdproduct_goods_loss";
    protected $fillable = [];
    public $timestamps = false;
}