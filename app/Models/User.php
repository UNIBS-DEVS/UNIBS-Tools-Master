<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'personal_mobile',
        'offical_mobile',
        'roles',
        'status',
        'manager_id',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',

        'roles' => 'array',
    ];

    /* ---------------- Relationships ---------------- */

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    /* ---------------- Role Helpers ---------------- */

    public function hasRole($roles)
    {
        return !empty(array_intersect((array) $roles, $this->roles ?? []));
    }

    public function hasAnyRole($roles)
    {
        $userRoles = $this->roles ?? [];

        if (is_string($userRoles)) {
            $userRoles = json_decode($userRoles, true);
        }

        return count(array_intersect($roles, $userRoles)) > 0;
    }
}
