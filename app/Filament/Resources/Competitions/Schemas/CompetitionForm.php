<?php

namespace App\Filament\Resources\Competitions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CompetitionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('parent_id'),
                Select::make('sport_id')
                    ->relationship('sport', 'name')
                    ->required(),
            ]);
    }
}
