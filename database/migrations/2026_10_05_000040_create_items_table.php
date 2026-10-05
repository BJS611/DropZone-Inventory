<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('sku')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignUuid('category_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('location_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('minimum_stock')->default(0);
            $table->enum('unit', ['PCS', 'UNIT', 'SET', 'BOX', 'METER'])->default('PCS');
            $table->enum('condition', ['GOOD', 'MINOR_DAMAGE', 'DAMAGED'])->default('GOOD');
            $table->enum('status', ['ACTIVE', 'INACTIVE', 'LOST', 'DISPOSED'])->default('ACTIVE');
            $table->string('image_url')->nullable();
            $table->timestamps();

            $table->index('category_id');
            $table->index('location_id');
            $table->index('supplier_id');
            $table->index('status');
            $table->index('condition');
            $table->index('quantity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
