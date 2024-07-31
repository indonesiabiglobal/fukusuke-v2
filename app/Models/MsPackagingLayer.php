<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsPackagingLayer extends Model
{
    use HasFactory;
    protected $table = "mspackaginglayer";
    protected $fillable = [];
    protected $guarded = [];
    public $timestamps = false;
}