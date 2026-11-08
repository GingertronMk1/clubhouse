<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Attributes\Boot;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Support\Str;

#[RouteKey('slug')]
trait HasSlug
{
    #[Boot]
    protected static function bootSlug(): void
    {
        static::saving(function (self $model): void {
            if ($model->isDirty('name') || ! $model->exists) {
                $model->slug = $model->generateSlug();
            }
        });
    }

    /** @return array<string, mixed> */
    protected function slugUniqueScope(): array
    {
        return [];
    }

    protected function generateSlug(): string
    {
        $baseSlug = substr(Str::slug($this->name) ?: 'item', 0, 255);

        if (! ($this->slugMustBeUnique ?? true)) {
            return $baseSlug;
        }

        $slug = $baseSlug;
        $suffix = 1;

        while ($this->newQueryWithoutScopes()
            ->where($this->slugUniqueScope())
            ->where('slug', $slug)
            ->when($this->exists, fn ($query) => $query->whereKeyNot($this->getKey()))
            ->exists()) {
            $suffix++;
            $ending = '-'.$suffix;
            $slug = substr($baseSlug, 0, 255 - strlen($ending)).$ending;
        }

        return $slug;
    }
}
