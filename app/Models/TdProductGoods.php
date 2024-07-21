<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TdProductGoods extends Model
{
    use HasFactory;
    protected $table = "tdproduct_goods";
    protected $fillable = [];
    public $timestamps = false;
}