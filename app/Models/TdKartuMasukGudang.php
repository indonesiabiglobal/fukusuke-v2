<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TdKartuMasukGudang extends Model
{
    use HasFactory;
    protected $table = "tr_kartu_masuk_gudang";
    protected $guarded = ['id'];
    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->printed_by = Auth::user()->id;
        });

        static::updating(function ($model) {
            $model->printed_by = Auth::user()->id;
        });
    }
}
