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
        Schema::create('company_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('default_rack_id')
                ->nullable()
                ->constrained('racks')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignUuid('product_id')
                ->constrained('products')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('company_code'); // "043", "044", "AM-600"
            $table->string('specification')->nullable();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_items');
    }
};
