<?php

use App\Models\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_scoring_events', function (Blueprint $table) {
            $table->foreignIdFor(Team::class)->nullable()->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('game_scoring_events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team_id');
        });
    }
};
