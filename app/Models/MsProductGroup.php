<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsProductGroup extends Model
{
    use HasFactory;
    protected $table = "msproduct_group";
    protected $guarded = ['id'];

    public $timestamps = false;

    // protected $fillable = [
    //     'title',
    //     'content',
    // ];
}
