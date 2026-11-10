<?php

use Illuminate\Support\Facades\Schema;

test('every name column has an index', function () {
    $tablesWithNames = collect(Schema::getTables())
        ->pluck('name')
        ->filter(fn (string $table): bool => Schema::hasColumn($table, 'name'));

    expect($tablesWithNames)->not->toBeEmpty();
    foreach ($tablesWithNames as $table) {
        expect(Schema::hasIndex($table, ['name']))->toBeTrue("Missing name index on {$table}.");
    }
});

test('the name index migration can be rolled back', function () {
    $migration = require database_path('migrations/2026_11_10_121921_add_name_indexes_to_tables.php');
    $tables = collect(Schema::getTables())->pluck('name')
        ->filter(fn (string $table): bool => Schema::hasColumn($table, 'name'));

    try {
        $migration->down();
        foreach ($tables as $table) {
            expect(Schema::hasIndex($table, ['name']))->toBeFalse("Name index remains on {$table}.");
        }
    } finally {
        $migration->up();
    }
});
