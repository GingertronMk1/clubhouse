<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Database\Factories\PositionFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    /** @use HasFactory<PositionFactory> */
    use HasFactory;

    use HasSlug, HasUuids, SoftDeletes;

    protected $fillable = ['name', 'description', 'preview_x', 'preview_y', 'sort_order', 'default_number'];

    /** @return array<string, mixed> */
    protected function slugUniqueScope(): array
    {
        return ['sport_id' => $this->sport_id];
    }

    /** @return BelongsTo<Sport, $this> */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }
}
