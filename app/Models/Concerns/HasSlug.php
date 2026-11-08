<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::saving(function (Model $model): void {
            if ($model->isDirty('name') || ! $model->exists) {
                $model->slug = Str::slug($model->name);
            }
        });
    }
}
