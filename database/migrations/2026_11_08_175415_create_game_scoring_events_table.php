<?php

use App\Models\Game;
use App\Models\ScoringEvent;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_scoring_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Game::class)->constrained();
            $table->foreignIdFor(ScoringEvent::class)->constrained();
            $table->unsignedInteger('timestamp')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_scoring_events');
    }
};
