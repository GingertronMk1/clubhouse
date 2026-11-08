<?php

namespace App\Http\Requests;

use App\Models\Competition;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompetitionRequest extends FormRequest
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
            'location_id' => ['required', 'uuid', Rule::exists('locations', 'id')->whereNull('deleted_at')],
            'parent_id' => ['nullable', 'uuid', Rule::exists('competitions', 'id')->whereNull('deleted_at'), function (string $attribute, mixed $value, Closure $fail): void {
                $competition = $this->route('competition');
                $visited = [];
                while (is_string($value) && $value !== '') {
                    if (($competition instanceof Competition && $value === $competition->id) || isset($visited[$value])) {
                        $fail('The parent competition cannot create a cycle.');

                        return;
                    }
                    $visited[$value] = true;
                    $value = Competition::query()->whereKey($value)->value('parent_id');
                }
            }],
        ];
    }
}
