<?php

namespace App\Filament\Resources\Positions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PositionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('preview_x')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('preview_y')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('order')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('default_number')
                    ->required()
                    ->numeric()
                    ->default(1),
                Select::make('sport_id')
                    ->relationship('sport', 'name')
                    ->required(),
            ]);
    }
}
