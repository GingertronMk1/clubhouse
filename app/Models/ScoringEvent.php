<?php

namespace App\Models;

use Database\Factories\ScoringEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'value', 'sort_order'])]
class ScoringEvent extends Model
{
    /** @use HasFactory<ScoringEventFactory> */
    use HasFactory;

    use HasUuids, SoftDeletes;

    protected $with = ['sport'];

    /** @return BelongsTo<Sport, $this> */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['value' => 'integer', 'sort_order' => 'integer'];
    }
}
