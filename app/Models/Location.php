<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    /** @use HasFactory<\Database\Factories\LocationFactory> */
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    public function coordinates(): Attribute
    {
        return Attribute::make(
            get: function (string $_value, array $attributes) {
                [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ] = $attributes;
                if (is_null($latitude) || is_null($longitude)) {
                    return null;
                }
                return [$latitude, $longitude];
            }
        );
    }
}
