<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SportRequest extends FormRequest
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
            'scoring' => ['present', 'array'],
            'scoring.*' => ['required', 'array:name,value'],
            'scoring.*.name' => ['required', 'string', 'max:255'],
            'scoring.*.value' => ['required', 'integer', 'between:-2147483648,2147483647'],
        ];
    }
}
