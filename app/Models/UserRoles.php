<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRoles extends Model
{
    use HasFactory;

    protected $table = 'userroles';

    protected $fillable = [
        'rolename',
        'description',
        'status',
        'trial523'
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public $timestamps = false;

    // Relationship dengan users melalui useraccess_role
    public function users()
    {
        return $this->belongsToMany(User::class, 'useraccess_role', 'roleid', 'userid');
    }

    // Relationship dengan useraccess_role
    public function accessRoles()
    {
        return $this->hasMany(UserAccessRole::class, 'roleid', 'id');
    }
}
