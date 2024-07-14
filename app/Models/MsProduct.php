<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsProduct extends Model
{
    use HasFactory;
    protected $table = "msproduct";
    protected $fillable = [];

    // protected $fillable = [
    //     'title',
    //     'content',
    // ];
}