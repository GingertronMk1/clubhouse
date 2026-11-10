<?php

namespace App\Http\Requests;

use App\Models\Game;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreGameSportEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $game = $this->route('game');

        return $game instanceof Game && Gate::allows('create-game-event', $game);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var Game $game */
        $game = $this->route('game');
        $competitors = $this->input('competitor_type') === 'team'
            ? array_filter([$game->team1_id, $game->team2_id])
            : $game->users()->pluck('game_user.id')->all();

        return [
            'sport_event_id' => ['required', 'uuid', Rule::exists('sport_events', 'id')->where(fn (Builder $query) => $query->where('sport_id', $game->sport_id))],
            'competitor_type' => ['required', Rule::in(['team', 'player'])],
            'competitor_id' => ['required', 'uuid', Rule::in($competitors)],
            'occurred_at' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }
}
