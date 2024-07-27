<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TdOrderLpk extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "tdorderlpk";
    protected $guarded = ['id'];
}