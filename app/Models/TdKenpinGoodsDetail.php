<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TdKenpinGoodsDetail extends Model
{
    use HasFactory;
    protected $table = "tdkenpin_goods_detail";
    protected $fillable = [];
    public $timestamps = false;
}