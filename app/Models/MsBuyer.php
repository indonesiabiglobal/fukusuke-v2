<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MsBuyer extends Model
{
    use HasFactory;
    protected $table = "msbuyer";
    protected $guarded = ['id'];

    public $timestamps = false;

    // protected $fillable = [
    //     'title',
    //     'content',
    // ];
}
