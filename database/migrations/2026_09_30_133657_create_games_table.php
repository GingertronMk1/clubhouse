<?php

use App\Models\Club;
use App\Models\Competition;
use App\Models\Location;
use App\Models\Sport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const string TABLE = 'games';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->index();
            $table->dateTime('start');
            $table->longText('description')->nullable();
            $table->longText('summary')->nullable();
            $table->foreignIdFor(Sport::class);
            $table->foreignIdFor(Competition::class);
            $table->foreignIdFor(Club::class, 'club1_id');
            $table->foreignIdFor(Club::class, 'club2_id');
            $table->json('score')->nullable();
            $table->foreignIdFor(Location::class);
            $table->timestamps();
            $table->softDeletes();
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
