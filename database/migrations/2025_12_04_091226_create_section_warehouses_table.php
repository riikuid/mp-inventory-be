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
        Schema::create('section_warehouses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('section_id')
                ->constrained('sections')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignUuid('warehouse_id')
                ->constrained('warehouses')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['section_id', 'warehouse_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_warehouses');
    }
};
