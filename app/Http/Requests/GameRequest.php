<?php

namespace App\Http\Requests;

use App\Models\Team;
use Closure;
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
            'team1_id' => ['required', 'uuid', Rule::exists('teams', 'id')->whereNull('deleted_at')],
            'team2_id' => ['required', 'uuid', 'different:team1_id', Rule::exists('teams', 'id')->whereNull('deleted_at')],
            'lineup' => ['sometimes', 'array'],
            'lineup.*.user_id' => ['required', 'uuid', 'distinct', Rule::exists('users', 'id')->whereNull('deleted_at'), function (string $attribute, mixed $value, Closure $fail): void {
                $index = explode('.', $attribute)[1];
                $teamId = $this->input("lineup.$index.team_id");
                if (! Team::query()->whereKey($teamId)->whereHas('club.users', fn ($query) => $query->whereKey($value))->exists()) {
                    $fail('The selected player must belong to the team’s club.');
                }
            }],
            'lineup.*.team_id' => ['required', 'uuid', Rule::in([$this->input('team1_id'), $this->input('team2_id')])],
            'lineup.*.position_id' => ['required', 'uuid', Rule::exists('positions', 'id')->where('sport_id', $this->input('sport_id'))->whereNull('deleted_at')],
            'lineup.*.jersey_number' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'location_id' => ['required', 'uuid', Rule::exists('locations', 'id')->whereNull('deleted_at')],
        ];
    }
}
