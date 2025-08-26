<?php

namespace App\Filament\Resources\Positions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PositionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('name'),
                TextEntry::make('preview_x')
                    ->numeric(),
                TextEntry::make('preview_y')
                    ->numeric(),
                TextEntry::make('order')
                    ->numeric(),
                TextEntry::make('default_number')
                    ->numeric(),
                TextEntry::make('sport.name'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
                TextEntry::make('deleted_at')
                    ->dateTime(),
            ]);
    }
}
