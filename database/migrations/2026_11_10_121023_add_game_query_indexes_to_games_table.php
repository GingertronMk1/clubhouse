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
        Schema::table('games', function (Blueprint $table) {
            $table->index(['deleted_at', 'start', 'id']);
            $table->index(['sport_id', 'deleted_at', 'start', 'id']);
            $table->index(['competition_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropIndex(['deleted_at', 'start', 'id']);
            $table->dropIndex(['sport_id', 'deleted_at', 'start', 'id']);
            $table->dropIndex(['competition_id', 'deleted_at']);
        });
    }
};
