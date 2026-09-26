<?php

namespace App\Models;

use Database\Factories\LocationFactory;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Appends(['coordinates'])]
class Location extends Model
{
    /** @use HasFactory<LocationFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'links' => 'array',
        ];
    }

    /**
     * @return Attribute<string[]>
     */
    public function address(): Attribute
    {
        return Attribute::make(
            get: function (?string $_value, array $attributes) {
                [
                    'address1' => $address1,
                    'address2' => $address2,
                    'address3' => $address3,
                    'postcode' => $postcode,
                    'city' => $city,
                    'country' => $country,
                ] = $attributes;
                return implode(PHP_EOL, array_filter([
                    $address1,
                    $address2,
                    $address3,
                    $postcode,
                    $city,
                    $country,
                ]));
            }
        );
    }

    /**
     * @return Attribute<[float, float]>
     */
    public function coordinates(): Attribute
    {
        return Attribute::make(
            get: function (?string $_value, array $attributes) {
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
