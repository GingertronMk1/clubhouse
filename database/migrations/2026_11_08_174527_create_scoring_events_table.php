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
        Schema::create('scoring_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Sport::class)->constrained();
            $table->string('name');
            $table->integer('value');
            $table->integer('sort_order');
            $table->timestamps();
            $table->softDeletes();
        });

        foreach (DB::table('sports')->orderBy('id')->cursor() as $sport) {
            $scoring = json_decode($sport->scoring ?? '[]', true, flags: JSON_THROW_ON_ERROR) ?? [];
            foreach ($scoring as $index => $event) {
                DB::table('scoring_events')->insert([
                    'id' => (string) Str::uuid7(),
                    'sport_id' => $sport->id,
                    'name' => $event['name'],
                    'value' => $event['value'],
                    'sort_order' => $index,
                    'created_at' => $sport->created_at,
                    'updated_at' => $sport->updated_at,
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

        foreach (DB::table('sports')->orderBy('id')->cursor() as $sport) {
            $scoring = DB::table('scoring_events')->where('sport_id', $sport->id)->whereNull('deleted_at')->orderBy('sort_order')->get(['name', 'value'])->map(fn ($event) => ['name' => $event->name, 'value' => $event->value])->all();
            DB::table('sports')->where('id', $sport->id)->update(['scoring' => json_encode($scoring, JSON_THROW_ON_ERROR)]);
        }

        Schema::dropIfExists('scoring_events');
    }
};
