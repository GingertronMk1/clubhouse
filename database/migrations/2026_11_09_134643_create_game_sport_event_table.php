<?php

use App\Models\Game;
use App\Models\SportEvent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_sport_event', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Game::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(SportEvent::class)->constrained()->cascadeOnDelete();
            $table->uuidMorphs('competitor');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_sport_event');
    }
};
