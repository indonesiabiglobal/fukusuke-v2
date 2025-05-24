<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'avatar',
        'email',
        'password',
        'remember_token'
    ];
    // public $timestamps = false;

    // custom created and updated
    const CREATED_AT = 'createdt';
    const UPDATED_AT = 'updatedt';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->createby = Auth::user()->empname;
            $model->updateby = Auth::user()->empname;
        });

        static::updating(function ($model) {
            $model->updateby = Auth::user()->empname;
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function useraccessrole()
    {
        return $this->belongsToMany(UserRoles::class, 'useraccess_role', 'userid', 'roleid');
    }
}
