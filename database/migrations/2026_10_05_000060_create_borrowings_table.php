<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('borrower_name');
            $table->string('borrower_identifier')->nullable();
            $table->string('borrower_contact')->nullable();
            $table->text('purpose')->nullable();
            $table->timestamp('borrowed_at')->nullable();
            $table->timestamp('expected_return_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->enum('status', [
                'PENDING',
                'BORROWED',
                'PARTIALLY_RETURNED',
                'RETURNED',
                'OVERDUE',
                'CANCELLED',
            ])->default('PENDING');
            $table->foreignUuid('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->foreignUuid('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('borrower_name');
            $table->index('expected_return_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
