<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Database\Factories\ClubFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'description'])]
class Club extends Model
{
    /** @use HasFactory<ClubFactory> */
    use HasFactory;

    use HasSlug, HasUuids, SoftDeletes;

    /** @return BelongsToMany<User, $this, ClubUser> */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->using(ClubUser::class)->withPivot('permissions')->withTimestamps();
    }

    /** @return HasMany<Team, $this> */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}
