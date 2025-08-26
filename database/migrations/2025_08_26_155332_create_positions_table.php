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
        Schema::create('positions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->index();
            $table->text('description')->nullable();
            $table->integer('preview_x')->default(0);
            $table->integer('preview_y')->default(0);
            $table->integer('order')->default(0);
            $table->integer('default_number')->default(1);
            $table->foreignIdFor(\App\Models\Sport::class)->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
