<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TdKenpinGoods extends Model
{
    use HasFactory;
    protected $table = "tdkenpin_goods";
    protected $fillable = [];
    public $timestamps = false;
}