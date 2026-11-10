<?php

namespace App\Http\Requests;

use App\Models\Sport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'events' => ['present', 'array'],
            'events.*' => ['required', 'array:id,name,description,score'],
            'events.*.id' => ['nullable', 'uuid', 'distinct', Rule::exists('sport_events', 'id')->where('sport_id', $sport instanceof Sport ? $sport->id : null)],
            'events.*.name' => ['required', 'string', 'max:255'],
            'events.*.description' => ['present', 'nullable', 'string'],
            'events.*.score' => ['required', 'integer', 'between:-2147483648,2147483647'],
        ];
    }

    /** @return array<int, \Closure(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $sport = $this->route('sport');
            if (! $sport instanceof Sport || $validator->errors()->isNotEmpty()) {
                return;
            }

            $events = $this->input('events', []);
            if (! is_array($events)) {
                return;
            }

            $recordedIds = DB::table('game_sport_event')
                ->whereIn('sport_event_id', $sport->events()->select('id'))
                ->distinct()->pluck('sport_event_id');
            $submitted = collect($events)->keyBy('id');
            foreach ($sport->events()->whereIn('id', $recordedIds)->get() as $event) {
                $entry = $submitted->get($event->id);
                if (! is_array($entry)) {
                    $validator->errors()->add('events', 'Events used in games cannot be removed.');
                } elseif (! is_numeric($entry['score'] ?? null) || (int) $entry['score'] !== $event->score) {
                    $validator->errors()->add('events', 'The score of an event used in games cannot be changed.');
                }
            }
        }];
    }
}
