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
        Schema::create('variant_components', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('variant_id')
                ->constrained('variants')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignUuid('component_id')
                ->constrained('components')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('quantity_needed')->default(1);
            $table->timestamps();

            $table->unique(['variant_id', 'component_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_components');
    }
};
