<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccessRole extends Model
{
    use HasFactory;

    protected $table = 'useraccess_role';

    protected $fillable = [
        'userid',
        'roleid',
        'rolemode',
        'trial523'
    ];

    public $timestamps = false;

    // Relationship dengan user
    public function user()
    {
        return $this->belongsTo(User::class, 'userid', 'id');
    }

    // Relationship dengan role
    public function role()
    {
        return $this->belongsTo(UserRoles::class, 'roleid', 'id');
    }
}
