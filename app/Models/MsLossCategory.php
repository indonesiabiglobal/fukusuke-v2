<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsLossCategory extends Model
{
    use HasFactory;
    protected $table = "mslosscategory";
    protected $guarded = ['id'];
    public $timestamps = false;
    // protected $fillable = [
    //     'title',
    //     'content',
    // ];
}
