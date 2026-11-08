<?php

namespace App\Http\Requests;

use App\Models\Sport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'club1_score' => ['present', 'array', 'list'],
            'club2_score' => ['present', 'array', 'list'],
            'club1_score.*' => ['required', 'array:name,value,count'],
            'club2_score.*' => ['required', 'array:name,value,count'],
            'club1_score.*.name' => ['required', 'string', 'distinct'],
            'club2_score.*.name' => ['required', 'string', 'distinct'],
            'club1_score.*.value' => ['required', 'integer'],
            'club2_score.*.value' => ['required', 'integer'],
            'club1_score.*.count' => ['required', 'integer', 'min:0'],
            'club2_score.*.count' => ['required', 'integer', 'min:0'],
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $sportId = $this->input('sport_id');
            if (! is_string($sportId)) {
                return;
            }
            $sport = Sport::query()->find($sportId);
            if (! $sport) {
                return;
            }
            foreach (['club1_score', 'club2_score'] as $field) {
                $scores = $this->input($field);
                if (! is_array($scores)) {
                    continue;
                }
                foreach ($scores as $index => $score) {
                    if (! is_array($score) || ! isset($score['name'], $score['value']) || ! is_string($score['name']) || (! is_int($score['value']) && ! is_string($score['value']))) {
                        continue;
                    }
                    $matches = $sport->scoring->contains(fn ($method) => $method->name === $score['name'] && (string) $method->value === (string) $score['value']);
                    if (! $matches) {
                        $validator->errors()->add("{$field}.{$index}.name", 'The scoring method must match the selected sport.');
                    }
                }
            }
        }];
    }
}
