<?php

namespace App\Models;

use Database\Factories\SportEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'description', 'score', 'is_team_event'])]
class SportEvent extends Model
{
    /** @use HasFactory<SportEventFactory> */
    use HasFactory;

    use HasUuids;

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['is_team_event' => 'boolean'];
    }

    /** @return BelongsTo<Sport, $this> */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }
}
