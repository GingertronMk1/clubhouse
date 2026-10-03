<?php

namespace App\Models;

use App\HasLocation;
use Database\Factories\CompetitionFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $name
 * @property ?string $description
 */
class Competition extends Model
{
    /** @use HasFactory<CompetitionFactory> */
    use HasFactory;

    use HasLocation;
    use HasUuids;
    use SoftDeletes;

    protected $with = ['parent'];

    /**
     * @return BelongsTo<Competition, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return HasMany<Competition, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
