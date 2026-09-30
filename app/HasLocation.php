<?php

namespace App;

use App\Models\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Model
 */
trait HasLocation
{
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
