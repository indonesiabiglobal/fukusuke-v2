<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsPackagingBox extends Model
{
    use HasFactory;
    protected $table = "mspackagingbox";
    protected $fillable = [];
    protected $guarded = [];
    public $timestamps = false;
}