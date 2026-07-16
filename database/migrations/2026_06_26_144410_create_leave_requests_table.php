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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('leave_type_id');

            $table->enum('duration', [
                'full day',
                'first half',
                'second half',
            ])->default('full day');

            $table->date('start_date');

            $table->date('end_date');

            $table->text('remarks')->nullable();

            $table->enum('status', [
                'submitted',
                'approved',
                'rejected',
                'cancelled'
            ])->default('submitted');

            $table->text('manager_remarks')->nullable();

            $table->date('manager_action_at')->nullable();

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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
