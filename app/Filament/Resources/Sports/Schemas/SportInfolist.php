<?php

namespace App\Filament\Resources\Sports\Schemas;

use App\Models\Sport;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('name'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('field_diagram')
                    ->placeholder('-')
                    ->columnSpan(1),
                KeyValueEntry::make('scoring')
                    ->keyLabel('Method')
                    ->valueLabel('Points')
                    ->columnSpan(1),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Sport $record): bool => $record->trashed()),
            ]);
    }
}
