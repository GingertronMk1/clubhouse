<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('game_sport_event', function (Blueprint $table) {
            $table->index('game_id');
            $table->index('sport_event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_sport_event', function (Blueprint $table) {
            $table->dropIndex(['game_id']);
            $table->dropIndex(['sport_event_id']);
        });
    }
};
