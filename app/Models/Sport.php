<?php

namespace App\Models;

use Database\Factories\SportFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sport extends Model
{
    /** @use HasFactory<SportFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'scoring' => 'array',
        ];
    }

    /**
     * @return HasMany<Position, $this>
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    public function teamSize(): int
    {
        $ret = 0;
        foreach ($this->positions as $position) {
            $ret += $position->per_side;
        }

        return $ret;
    }
}
