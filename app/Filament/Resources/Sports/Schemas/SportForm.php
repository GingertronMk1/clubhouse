<?php

namespace App\Filament\Resources\Sports\Schemas;

use Closure;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                KeyValue::make('scoring')
                    ->rules([
                        fn (): Closure => function (string $attribute, $value, Closure $fail) {
                            foreach ($value as ['value' => $innerValue]) {
                                if (! is_numeric($innerValue)) {
                                    $fail('The value of a score should only be numeric');
                                }
                            }
                        }])
                    ->keyLabel('Score type')
                    ->valueLabel('Score value'),
                Textarea::make('field_diagram')
                    ->columnSpanFull(),
            ]);
    }
}
