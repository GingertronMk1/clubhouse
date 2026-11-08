<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class LocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array{name: list<string>, description: list<string>, additional_information: list<string|Closure>} */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'additional_information' => ['required', 'string', 'json', function (string $attribute, mixed $value, Closure $fail): void {
                if (is_string($value) && ! is_array(json_decode($value, true))) {
                    $fail('The additional information must be a JSON object or array.');
                }
            }],
        ];
    }
}
