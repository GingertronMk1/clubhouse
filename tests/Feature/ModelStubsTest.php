<?php

use Database\Factories\StubRecordFactory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

test('generated models and migrations support UUIDv7 identifiers and soft deletes', function () {
    $applicationPath = app_path();
    $databasePath = database_path();
    $temporaryPath = sys_get_temp_dir().'/clubhouse-stubs-'.Str::random(16);

    $this->app->getNamespace();
    File::ensureDirectoryExists($temporaryPath.'/app/Models');
    File::ensureDirectoryExists($temporaryPath.'/database/migrations');

    try {
        $this->app->useAppPath($temporaryPath.'/app');
        $this->app->useDatabasePath($temporaryPath.'/database');

        $this->artisan('make:model', [
            'name' => 'StubRecord',
            '--factory' => true,
            '--migration' => true,
            '--no-interaction' => true,
        ])->assertSuccessful();

        require $temporaryPath.'/app/Models/StubRecord.php';
        require $temporaryPath.'/database/factories/StubRecordFactory.php';

        $migrationPath = File::glob($temporaryPath.'/database/migrations/*_create_stub_records_table.php')[0];
        $migration = require $migrationPath;
        $migration->up();

        $record = StubRecordFactory::new()->create();

        expect(Str::isUuid($record->getKey(), version: 7))->toBeTrue();
        $this->assertModelExists($record);

        $record->delete();

        $this->assertSoftDeleted($record);
        expect($record->newQuery()->find($record->getKey()))->toBeNull();

        $record->restore();

        $this->assertNotSoftDeleted($record);
        expect($record->newQuery()->find($record->getKey()))->not->toBeNull();

        $migration->down();
    } finally {
        $this->app->useAppPath($applicationPath);
        $this->app->useDatabasePath($databasePath);
        File::deleteDirectory($temporaryPath);
    }
});
