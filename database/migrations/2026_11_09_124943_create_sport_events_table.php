<?php

use App\Models\Sport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sport_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Sport::class)->constrained();
            $table->string('name');
            $table->text('description');
            $table->integer('score')->default(0);
            $table->timestamps();
        });

        foreach (DB::table('sports')->get(['id', 'scoring']) as $sport) {
            $scoring = $sport->scoring ?? '[]';
            if (! is_string($scoring)) {
                throw new UnexpectedValueException('Sport scoring must be JSON.');
            }
            $events = json_decode($scoring, true, 512, JSON_THROW_ON_ERROR);
            if (! is_array($events)) {
                throw new UnexpectedValueException('Sport scoring must be an array.');
            }
            foreach ($events as $event) {
                if (! is_array($event) || ! isset($event['name'], $event['value'])) {
                    throw new UnexpectedValueException('Sport scoring events must have a name and value.');
                }
                DB::table('sport_events')->insert([
                    'id' => (string) Str::uuid(), 'sport_id' => $sport->id,
                    'name' => $event['name'], 'description' => '', 'score' => $event['value'],
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        }

        Schema::table('sports', function (Blueprint $table) {
            $table->dropColumn('scoring');
        });
    }

    public function down(): void
    {
        Schema::table('sports', function (Blueprint $table) {
            $table->json('scoring')->nullable();
        });

        foreach (DB::table('sports')->get(['id']) as $sport) {
            $scoring = DB::table('sport_events')->where('sport_id', $sport->id)->get(['name', 'score'])
                ->map(fn ($event) => ['name' => $event->name, 'value' => $event->score]);
            DB::table('sports')->where('id', $sport->id)->update(['scoring' => $scoring->toJson()]);
        }

        Schema::dropIfExists('sport_events');
    }
};
