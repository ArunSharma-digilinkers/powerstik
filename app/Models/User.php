<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // Roles are plain strings (project rule: no PHP enums).
    public const ROLE_ADMIN = 'admin';   // everything, including users and system

    public const ROLE_EDITOR = 'editor'; // site content

    public const ROLE_SALES = 'sales';   // leads inbox only

    public const ROLES = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_EDITOR => 'Editor',
        self::ROLE_SALES => 'Sales',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /** Admins pass every role check. */
    public function hasRole(string ...$roles): bool
    {
        return $this->role === self::ROLE_ADMIN || in_array($this->role, $roles, true);
    }
}
