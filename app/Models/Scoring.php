<?php

namespace App\Models;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/** @implements Arrayable<string, string|int> */
class Scoring implements Arrayable, JsonSerializable
{
    /** @param array{name: string, value: int} $attributes */
    public function __construct(array $attributes)
    {
        $this->name = $attributes['name'];
        $this->value = (int) $attributes['value'];
    }

    public readonly string $name;

    public readonly int $value;

    /** @return array{name: string, value: int} */
    public function toArray(): array
    {
        return ['name' => $this->name, 'value' => $this->value];
    }

    /** @return array{name: string, value: int} */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
