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
        Schema::create('units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // $table->uuid('variant_id')->nullable();
            // $table->uuid('component_id')->nullable();
            // $table->uuid('parent_unit_id')->nullable();
            $table->string('qr_value')->unique();
            $table->enum('status', ['ACTIVE', 'BOUND', 'CONSUMED', 'DELETED'])->default('ACTIVE');
            $table->foreignUuid('rack_id')
                ->nullable()
                ->constrained('racks')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->integer('print_count')->default(1);
            $table->timestamp('last_printed_at')->nullable();
            $table->foreignUuid('last_printed_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('synced_at')->nullable();
            $table->foreignUuid('created_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignUuid('variant_id')->nullable()
                ->references('id')->on('variants')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignUuid('component_id')->nullable()
                ->references('id')->on('components')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignUuid('parent_unit_id')->nullable()
                ->references('id')->on('units')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
