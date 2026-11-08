<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/** @property array<string>|null $permissions */
class ClubUser extends Pivot
{
    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['permissions' => 'array'];
    }
}
