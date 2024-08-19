<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsLakbanSeitai extends Model
{
    use HasFactory;
    protected $table = "mslakbanseitai";
    public $timestamps = false;
    protected $guarded = ['id'];
}
