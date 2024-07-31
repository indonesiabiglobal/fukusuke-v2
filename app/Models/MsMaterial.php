<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsMaterial extends Model
{
    use HasFactory;
    protected $table = "msmaterial";
    protected $guarded = ['id'];

    public $timestamps = false;
}
