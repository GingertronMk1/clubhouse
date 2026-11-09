<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Permission;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property bool $superadmin
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    use HasUuids, SoftDeletes;

    /** @return BelongsToMany<Game, $this, GameUser> */
    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class)->using(GameUser::class)->withPivot(['team', 'position_id', 'jersey_number'])->withTimestamps();
    }

    /** @return BelongsToMany<Club, $this, ClubUser> */
    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class)->using(ClubUser::class)->withPivot('permissions')->withTimestamps();
    }

    /** @param Permission|array<Permission> $permissions */
    public function hasClubPermission(Club $club, Permission|array $permissions, bool $includeTrashed = false): bool
    {
        $permissions = is_array($permissions) ? $permissions : [$permissions];

        if ($permissions === []) {
            return false;
        }

        if ($this->superadmin) {
            return true;
        }

        $clubs = $this->clubs();

        if ($includeTrashed) {
            $clubs->withTrashed();
        }

        $membership = $clubs->whereKey($club->getKey())->first()?->pivot;

        if (! $membership instanceof ClubUser || ! is_array($membership->permissions)) {
            return false;
        }

        foreach ($permissions as $permission) {
            if (! in_array($permission->value, $membership->permissions, true)) {
                return false;
            }
        }

        return true;
    }

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
            'superadmin' => 'boolean',
        ];
    }
}
