<?php

namespace App\Filament\Resources\Games\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class GameForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                DateTimePicker::make('start')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('summary')
                    ->columnSpanFull(),
                Select::make('competition_id')
                    ->relationship('competition', 'name')
                    ->required(),
                Textarea::make('score')
                    ->required()
                    ->default('{}')
                    ->columnSpanFull(),
            ]);
    }
}
