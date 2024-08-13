<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsLossKenpin extends Model
{
    use HasFactory;
    protected $table = "mslosskenpin";
    protected $guarded = ['id'];

    public $timestamps = false;

    // protected $fillable = [
    //     'title',
    //     'content',
    // ];
}
