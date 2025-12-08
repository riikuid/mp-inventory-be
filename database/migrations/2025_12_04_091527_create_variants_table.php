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
        Schema::create('variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_item_id')
                ->constrained('company_items')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignUuid('rack_id')
                ->nullable()
                ->constrained('racks')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignUuid('brand_id')
                ->nullable()
                ->constrained('brands')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->string('name');
            $table->string('uom');
            $table->json('specification')->nullable();
            $table->json('manuf_code')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // $table->unique(['company_item_id', 'brand_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};
