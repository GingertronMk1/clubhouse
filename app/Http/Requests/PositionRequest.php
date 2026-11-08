<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'preview_x' => ['required', 'integer', 'between:-2147483648,2147483647'],
            'preview_y' => ['required', 'integer', 'between:-2147483648,2147483647'],
            'sort_order' => ['required', 'integer', 'between:-2147483648,2147483647'],
            'default_number' => ['required', 'integer', 'between:-2147483648,2147483647'],
        ];
    }
}
