<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsLossClass extends Model
{
    use HasFactory;
    protected $table = "mslossclass";
    protected $fillable = [];
    protected $guarded = [];
    public $timestamps = false;
    // protected $fillable = [
    //     'title',
    //     'content',
    // ];
}