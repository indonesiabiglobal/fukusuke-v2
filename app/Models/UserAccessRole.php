<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccessRole extends Model
{
    use HasFactory;

    protected $table = 'useraccess_role';

    // DISABLE automatic timestamps
    public $timestamps = false;

    // PENTING: Set incrementing ke false karena tidak ada kolom id
    public $incrementing = false;

    // PENTING: Set primary key ke composite key atau null
    protected $primaryKey = null; // Atau ['userid', 'roleid'] jika pakai composite

    protected $fillable = [
        'userid',
        'roleid',
        'rolemode',
        'trial523'
    ];

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
