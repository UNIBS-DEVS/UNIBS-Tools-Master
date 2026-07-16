<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advance_request_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('advance_req_id')
                ->constrained('advance_requests')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained('expense_categories')
                ->restrictOnDelete();

            $table->decimal('requested_amount', 12, 2);

            $table->text('expense_reason');

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advance_request_items');
    }
};
