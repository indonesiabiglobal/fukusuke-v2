<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TdOrders extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "tdorder";
    protected $fillable = [
        'po_no',
        'product_id',
        'order_qty',
    ];
}