<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    use HasFactory;

    protected $table = 'access';

    protected $fillable = [
        'access_name',
        'description',
        'status',
    ];

    public $timestamps = false;

    // Relationship dengan useraccess_role
    public function role()
    {
        return $this->belongsToMany(Role::class, 'role_access', 'access_id', 'role_id');
    }
}
