<?php

namespace App\Http\Requests;

use App\Models\Sport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $sport = $this->route('sport');

        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'scoring' => ['present', 'array', 'list'],
            'scoring.*' => ['required', 'array:id,name,value'],
            'scoring.*.id' => ['nullable', 'uuid', 'distinct', Rule::prohibitedIf(! $sport instanceof Sport), Rule::exists('scoring_events', 'id')->where('sport_id', $sport instanceof Sport ? $sport->getKey() : null)->whereNull('deleted_at')],
            'scoring.*.name' => ['required', 'string', 'max:255'],
            'scoring.*.value' => ['required', 'integer', 'between:-2147483648,2147483647'],
        ];
    }
}
