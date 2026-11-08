<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GameRequest extends FormRequest
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
            'start' => ['nullable', 'date'],
            'description' => ['required', 'string'],
            'summary' => ['required', 'string'],
            'sport_id' => ['required', 'uuid', Rule::exists('sports', 'id')->whereNull('deleted_at')],
            'competition_id' => ['required', 'uuid', Rule::exists('competitions', 'id')->whereNull('deleted_at')],
            'club1_id' => ['required', 'uuid', Rule::exists('teams', 'id')->whereNull('deleted_at')],
            'club2_id' => ['required', 'uuid', 'different:club1_id', Rule::exists('teams', 'id')->whereNull('deleted_at')],
            'location_id' => ['required', 'uuid', Rule::exists('locations', 'id')->whereNull('deleted_at')],
        ];
    }
}
