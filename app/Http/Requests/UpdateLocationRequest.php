<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'string|required|max:255',
            'description' => 'string|nullable',
            'latitude' => 'numeric|nullable',
            'longitude' => 'numeric|nullable',
            'address_1' => 'string|nullable',
            'address_2' => 'string|nullable',
            'address_3' => 'string|nullable',
            'city' => 'string|nullable',
            'country' => 'string|nullable',
            'postcode' => 'string|nullable',
            'links' => 'array',
            'links.*.title' => 'string|required|max:255',
            'links.*.url' => 'string|required|url',
        ];
    }
}
