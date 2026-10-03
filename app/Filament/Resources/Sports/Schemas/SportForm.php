<?php

namespace App\Filament\Resources\Sports\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
                        fn(): \Closure => function (string $attribute, $value, \Closure $fail) {
                            $values = array_values($value);
                            foreach ($values as $value) {
                                if (!is_numeric($value))
                                    $fail(__('Not numeric'));
                            }

                        },
                    ])
                    ->addActionLabel('Add scoring method'),
                Textarea::make('field_diagram')
                    ->columnSpanFull(),
            ]);
    }
}
