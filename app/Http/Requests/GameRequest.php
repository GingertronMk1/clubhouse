<?php

namespace App\Http\Requests;

use App\Models\Game;
use App\Models\GameScoringEvent;
use App\Models\ScoringEvent;
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
        $game = $this->route('game');

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
            'scoring_events' => ['sometimes', 'array', 'list'],
            'scoring_events.*' => ['required', 'array:id,scoring_event_id,team_id,timestamp'],
            'scoring_events.*.id' => ['nullable', 'uuid', 'distinct', Rule::prohibitedIf(! $game instanceof Game), Rule::exists('game_scoring_events', 'id')->where('game_id', $game instanceof Game ? $game->getKey() : null)->whereNull('deleted_at')],
            'scoring_events.*.scoring_event_id' => ['required', 'uuid', Rule::exists('scoring_events', 'id')->where('sport_id', $this->input('sport_id'))],
            'scoring_events.*.team_id' => ['nullable', 'uuid', Rule::in([$this->input('club1_id'), $this->input('club2_id')])],
            'scoring_events.*.timestamp' => ['nullable', 'integer', 'between:0,4294967295'],
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            $game = $this->route('game');
            $events = $this->input('scoring_events');
            if (! is_array($events)) {
                if ($game instanceof Game && ($game->sport_id !== $this->input('sport_id') || $game->club1_id !== $this->input('club1_id') || $game->club2_id !== $this->input('club2_id')) && $game->scoringEvents()->exists()) {
                    $validator->errors()->add('scoring_events', 'Update the scoring events when changing the sport or teams.');
                }

                return;
            }
            $existing = $game instanceof Game ? $game->scoringEvents()->without(['game', 'scoringEvent', 'team'])->get()->keyBy('id') : collect();
            $scoringTypes = ScoringEvent::query()->withTrashed()->without('sport')->whereIn('id', collect($events)->pluck('scoring_event_id')->filter()->all())->get()->keyBy('id');
            foreach ($events as $index => $event) {
                if (! is_array($event)) {
                    continue;
                }
                $old = isset($event['id']) && is_string($event['id']) ? $existing->get($event['id']) : null;
                $type = isset($event['scoring_event_id']) && is_string($event['scoring_event_id']) ? $scoringTypes->get($event['scoring_event_id']) : null;
                if (empty($event['team_id']) && (! $old instanceof GameScoringEvent || $old->team_id !== null)) {
                    $validator->errors()->add("scoring_events.{$index}.team_id", 'Choose the team that scored.');
                }
                if ($type instanceof ScoringEvent && $type->trashed() && (! $old instanceof GameScoringEvent || $old->scoring_event_id !== $type->id)) {
                    $validator->errors()->add("scoring_events.{$index}.scoring_event_id", 'This scoring type is no longer available.');
                }
            }
        }];
    }
}
