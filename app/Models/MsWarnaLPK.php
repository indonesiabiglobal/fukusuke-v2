<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsWarnaLPK extends Model
{
    use HasFactory;
    protected $table = "mswarnalpk";
    public $timestamps = false;
    protected $guarded = ['id'];
}
