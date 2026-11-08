<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->renameColumn('club1_id', 'team1_id');
            $table->renameColumn('club2_id', 'team2_id');
        });
    }

    public function down(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->renameColumn('team1_id', 'club1_id');
            $table->renameColumn('team2_id', 'club2_id');
        });
    }
};
