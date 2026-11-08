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
        Schema::table('sports', function (Blueprint $table): void {
            $table->json('scoring')->nullable();
        });

        DB::table('sports')->orderBy('id')->chunk(100, function ($sports): void {
            foreach ($sports as $sport) {
                $scoring = DB::table('scorings')->where('sport_id', $sport->id)
                    ->whereNull('deleted_at')->orderBy('created_at')->orderBy('id')
                    ->get(['name', 'value'])->map(fn ($score) => [
                        'name' => $score->name,
                        'value' => (int) $score->value,
                    ])->all();
                DB::table('sports')->where('id', $sport->id)->update(['scoring' => json_encode($scoring, JSON_THROW_ON_ERROR)]);
            }
        });

        Schema::dropIfExists('scorings');
    }

    public function down(): void
    {
        Schema::create('scorings', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Sport::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('value');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('sports')->orderBy('id')->chunk(100, function ($sports): void {
            foreach ($sports as $sport) {
                foreach (json_decode($sport->scoring ?? '[]', true, 512, JSON_THROW_ON_ERROR) as $score) {
                    DB::table('scorings')->insert([
                        'id' => (string) Str::uuid7(), 'sport_id' => $sport->id,
                        'name' => $score['name'], 'value' => $score['value'],
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
            }
        });

        Schema::table('sports', function (Blueprint $table): void {
            $table->dropColumn('scoring');
        });
    }
};
