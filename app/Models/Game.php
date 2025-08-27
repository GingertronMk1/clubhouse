<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    /** @use HasFactory<\Database\Factories\GameFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;

    protected $casts = [
        'score' => 'json',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function sport(): BelongsTo
    {
        return $this->competition->sport();
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Person::class);
    }

    public function getScoringBreakdownAttribute(): string
    {
        $scoring = $this->sport->scoring;
        $total = 0;
        $_ret = [];
        foreach ($this->score as $score => $amount) {
            $_ret[] = sprintf('%s: %d', $score, $amount);
            $total += ($scoring[$score] ?? 0) * $amount;
        }

        return sprintf('%s; %d', implode(', ', $_ret), $total);
    }
}
