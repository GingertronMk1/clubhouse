<?php

use App\Models\ScoringEvent;
use App\Models\Sport;
use Database\Seeders\ScoringEventSeeder;
use Database\Seeders\SportSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

test('scoring events belong to a sport and are ordered and soft deleted', function () {
    $sport = Sport::factory()->create();
    $second = ScoringEvent::factory()->for($sport)->create(['name' => 'Conversion', 'value' => 2, 'sort_order' => 1]);
    $first = ScoringEvent::factory()->for($sport)->create(['name' => 'Try', 'value' => 4, 'sort_order' => 0]);

    expect(Str::isUuid($first->id, version: 7))->toBeTrue();
    expect($first->fresh()->relationLoaded('sport'))->toBeTrue();
    expect($first->sport->is($sport))->toBeTrue();
    expect($first->fresh()->value)->toBe(4);
    expect($sport->scoringEvents()->pluck('name')->all())->toBe(['Try', 'Conversion']);
    $second->delete();
    $this->assertSoftDeleted($second);
    expect($sport->scoringEvents()->count())->toBe(1);
});

test('scoring event seeding is repeatable and keeps events attached to their sport', function () {
    $this->seed(SportSeeder::class);
    $this->seed(ScoringEventSeeder::class);

    expect(ScoringEvent::query()->count())->toBe(15);
    $rugby = Sport::query()->where('name', 'Rugby League')->firstOrFail();
    expect($rugby->scoringEvents()->pluck('name')->all())->toBe(['Try', 'Conversion', 'Penalty goal', 'Drop goal']);
});

test('migration preserves existing scoring data and its order including for deleted sports', function () {
    $sport = Sport::factory()->create();
    ScoringEvent::factory()->for($sport)->create(['name' => 'Try', 'value' => 4, 'sort_order' => 0]);
    ScoringEvent::factory()->for($sport)->create(['name' => 'Conversion', 'value' => 2, 'sort_order' => 1]);
    $sport->delete();
    $migration = require database_path('migrations/2026_11_08_174527_create_scoring_events_table.php');

    $migration->down();
    $legacy = json_decode(DB::table('sports')->where('id', $sport->id)->value('scoring'), true);
    $migration->up();

    expect($legacy)->toBe([['name' => 'Try', 'value' => 4], ['name' => 'Conversion', 'value' => 2]]);
    expect(ScoringEvent::query()->where('sport_id', $sport->id)->orderBy('sort_order')->get(['name', 'value'])->map->only(['name', 'value'])->all())->toBe($legacy);
});

test('editing sport scoring keeps owned event ids and rejects other sports events', function () {
    $sport = Sport::factory()->create();
    $event = ScoringEvent::factory()->for($sport)->create(['name' => 'Try', 'value' => 4]);
    $otherEvent = ScoringEvent::factory()->create();
    $data = ['name' => $sport->name, 'description' => $sport->description, 'scoring' => [['id' => $event->id, 'name' => 'Try', 'value' => 5]]];

    $this->put(route('sports.update', $sport), $data)->assertRedirect(route('sports.index'));
    expect($event->refresh()->value)->toBe(5);
    expect($sport->scoringEvents()->firstOrFail()->id)->toBe($event->id);
    $this->put(route('sports.update', $sport), [...$data, 'scoring' => [['id' => $otherEvent->id, 'name' => 'Other', 'value' => 2]]])->assertSessionHasErrors('scoring.0.id');
    expect($otherEvent->refresh()->name)->not->toBe('Other');
});
