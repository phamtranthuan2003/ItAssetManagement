<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    public const UPDATED_AT = null;

    protected $fillable = [
        'username',
        'password',
        'fullname',
        'email',
        'role',
        'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['status' => 'integer'];
    }

    public function isSuperAdmin(): bool
    {
        return (int) $this->getKey() === 6;
    }

    public function roleRecord(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role', 'code');
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return DB::table('roles')
            ->join('role_permissions', 'role_permissions.role_id', '=', 'roles.id')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('roles.code', $this->role)
            ->where('permissions.code', $permission)
            ->exists();
    }

    public function landingRoute(): string
    {
        foreach ([
            'dashboard.view' => 'dashboard',
            'devices.view' => 'devices.index',
            'tickets.view' => 'tickets.index',
            'maintenance.view' => 'maintenance.index',
            'departments.view' => 'departments.index',
            'employees.view' => 'employees.index',
            'reports.view' => 'reports.index',
            'users.manage' => 'users.index',
            'roles.manage' => 'roles.index',
        ] as $permission => $route) {
            if ($this->hasPermission($permission)) {
                return $route;
            }
        }

        return 'profile';
    }
}
