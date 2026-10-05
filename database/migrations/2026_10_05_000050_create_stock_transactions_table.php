<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('item_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['IN', 'OUT', 'ADJUSTMENT', 'TRANSFER', 'RETURN']);
            $table->unsignedInteger('quantity');
            $table->foreignUuid('from_location_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete();
            $table->foreignUuid('to_location_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete();
            $table->text('note')->nullable();
            $table->foreignUuid('performed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->index('item_id');
            $table->index('type');
            $table->index('performed_by');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
