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
            'events' => ['present', 'array'],
            'events.*' => ['required', 'array:name,description,score,is_team_event'],
            'events.*.name' => ['required', 'string', 'max:255'],
            'events.*.description' => ['present', 'nullable', 'string'],
            'events.*.score' => ['required', 'integer', 'between:-2147483648,2147483647'],
            'events.*.is_team_event' => ['sometimes', 'boolean'],
        ];
    }
}
