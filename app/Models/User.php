<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'username',
        'empname',
        'email',
        'password',
        'newpass',
        'newpass_key',
        'newpass_time',
        'empid',
        'roleid',
        'territory_ix',
        'code',
        'last_ip',
        'last_login',
        'status',
        'createby',
        'createdt',
        'updateby',
        'updatedt',
        'trial520',
        'remember_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'newpass_time' => 'datetime',
        'last_login' => 'datetime',
        'createdt' => 'datetime',
        'updatedt' => 'datetime',
        'status' => 'integer',
    ];

    // Relationship dengan useraccess_role
    public function accessRoles()
    {
        return $this->hasMany(UserAccessRole::class, 'userid', 'id');
    }

    // Relationship dengan userroles melalui useraccess_role
    public function roles()
    {
        return $this->belongsToMany(UserRoles::class, 'useraccess_role', 'userid', 'roleid');
    }
}
