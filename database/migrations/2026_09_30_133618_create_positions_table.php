<?php

use App\Models\Sport;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const string TABLE = 'positions';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->index();
            $table->longText('description')->nullable();
            $table->integer('preview_x')->default(0);
            $table->integer('preview_y')->default(0);
            $table->integer('sort_order')->default(0);
            $table->integer('default_number')->default(1);
            $table->integer('per_side')->default(1);
            $table->foreignIdFor(Sport::class);
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
