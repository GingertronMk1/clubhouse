<?php

namespace App\Models;

use App\Permission;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
            'permissions' => 'array',
        ];
    }

    /**
     * @return Attribute<Permission[], string>
     */
    protected function permissions(): Attribute
    {
        return Attribute::make(
            get: fn (string $permissions) => array_filter(
                array_map(
                    fn (string $permission) => Permission::tryFrom($permission),
                    json_decode($permissions, true)
                )
            ),
            set: fn (array $permissions) => json_encode(
                array_map(
                    fn (Permission $permission) => $permission->value,
                    $permissions
                )
            )
        );
    }

    public function hasPermission(string|Permission $permission): bool
    {
        if (is_string($permission)) {
            $permission = Permission::tryFrom($permission);
        }

        return in_array($permission, $this->permissions);
    }
}
