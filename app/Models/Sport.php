<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Database\Factories\SportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'description', 'scoring'])]
class Sport extends Model
{
    /** @use HasFactory<SportFactory> */
    use HasFactory;

    use HasSlug, HasUuids, SoftDeletes;

    /** @return HasMany<Position, $this> */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['scoring' => AsCollection::of(Scoring::class)];
    }
}
