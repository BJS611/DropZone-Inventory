<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowing_items', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('borrowing_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('item_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('returned_quantity')->default(0);
            $table->enum('condition_before', ['GOOD', 'MINOR_DAMAGE', 'DAMAGED'])->default('GOOD');
            $table->enum('condition_after', ['GOOD', 'MINOR_DAMAGE', 'DAMAGED'])->nullable();
            $table->timestamps();

            $table->index('borrowing_id');
            $table->index('item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowing_items');
    }
};
