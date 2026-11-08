<?php

use App\Models\Competition;
use App\Models\Location;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->dateTime('start');
            $table->text('description');
            $table->text('summary');
            $table->foreignIdFor(Sport::class)->constrained();
            $table->foreignIdFor(Competition::class)->constrained();
            $table->foreignIdFor(Team::class, 'team1_id')->constrained('teams');
            $table->foreignIdFor(Team::class, 'team2_id')->constrained('teams');
            $table->json('score');
            $table->foreignIdFor(Location::class)->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
