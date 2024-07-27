<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsProductType extends Model
{
    use HasFactory;
    protected $table = "msproduct_type";
    protected $guarded = ['id'];

    public $timestamps = false;

    // protected $fillable = [
    //     'title',
    //     'content',
    // ];
}
