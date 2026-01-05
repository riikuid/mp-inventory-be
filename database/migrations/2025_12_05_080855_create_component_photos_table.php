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
        Schema::create('component_photos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('component_id');
            $table->string('file_path');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);

            $table->foreign('component_id')
                ->references('id')->on('components')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('component_photos');
    }
};
