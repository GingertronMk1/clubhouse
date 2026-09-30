<?php

namespace App\Models;

use Database\Factories\PositionFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $name
 * @property ?string $description
 * @property int $preview_x
 * @property int $preview_y
 * @property int $sort_order
 * @property int $default_number
 * @property int $per_side
 */
class Position extends Model
{
    /** @use HasFactory<PositionFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;

    protected $with = ['sport'];

    /**
     * @return BelongsTo<Sport, $this>
     */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }
}
