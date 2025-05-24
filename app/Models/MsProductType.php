<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MsProductType extends Model
{
    use HasFactory;
    protected $table = "msproduct_type";
    protected $guarded = ['id'];

    // public $timestamps = false;

    // custom created and updated
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = Auth::user()->username;
            $model->updated_by = Auth::user()->username;
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::user()->username;
        });
    }

    // protected $fillable = [
    //     'title',
    //     'content',
    // ];
}
