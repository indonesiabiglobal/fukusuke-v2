<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRoles extends Model
{
    use HasFactory;

    protected $table = 'user_roles';

    protected $fillable = [
        'user_id',
        'role_id',
        'status',
    ];

    public $timestamps = false;

    // Relationship dengan users melalui useraccess_role
    public function users()
    {
        return $this->belongsToMany(User::class, 'useraccess_role', 'roleid', 'userid');
    }

    // Relationship dengan useraccess_role
    public function roles()
    {
        return $this->hasMany(Role::class, 'roleid', 'id');
    }
}
