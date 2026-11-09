<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_user', function (Blueprint $table) {
            $table->uuid('id')->nullable()->unique();
        });

        DB::table('game_user')->orderBy('game_id')->orderBy('user_id')->get(['game_id', 'user_id'])->each(function ($row): void {
            DB::table('game_user')->where('game_id', $row->game_id)->where('user_id', $row->user_id)
                ->update(['id' => (string) Str::uuid7()]);
        });

        Schema::table('game_user', function (Blueprint $table) {
            $table->uuid('id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('game_user', function (Blueprint $table) {
            $table->dropUnique(['id']);
            $table->dropColumn('id');
        });
    }
};
