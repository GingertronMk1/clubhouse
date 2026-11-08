<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->renameColumn('score', 'result');
            $table->json('club1_score')->nullable();
            $table->json('club2_score')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn(['club1_score', 'club2_score']);
            $table->renameColumn('result', 'score');
        });
    }
};
