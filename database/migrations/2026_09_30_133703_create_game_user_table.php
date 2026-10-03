<?php

use App\Models\Club;
use App\Models\Game;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const string TABLE = 'game_user';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->foreignIdFor(Game::class);
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Club::class);
            $table->foreignIdFor(Position::class);
            $table->integer('number')->nullable();
            $table->longText('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE);
    }
};
