<?php

namespace App\Filament\Resources\Competitions;

use App\Filament\Resources\Competitions\Pages\CreateCompetition;
use App\Filament\Resources\Competitions\Pages\EditCompetition;
use App\Filament\Resources\Competitions\Pages\ListCompetitions;
use App\Filament\Resources\Competitions\Pages\ViewCompetition;
use App\Filament\Resources\Competitions\Schemas\CompetitionForm;
use App\Filament\Resources\Competitions\Schemas\CompetitionInfolist;
use App\Filament\Resources\Competitions\Tables\CompetitionsTable;
use App\Models\Competition;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompetitionResource extends Resource
{
    protected static ?string $model = Competition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CompetitionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CompetitionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompetitionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompetitions::route('/'),
            'create' => CreateCompetition::route('/create'),
            'view' => ViewCompetition::route('/{record}'),
            'edit' => EditCompetition::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
