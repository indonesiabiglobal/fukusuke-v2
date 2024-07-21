<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsEmployee extends Model
{
    use HasFactory;
    protected $table = "msemployee";
    protected $fillable = [];

    // protected $fillable = [
    //     'title',
    //     'content',
    // ];
}