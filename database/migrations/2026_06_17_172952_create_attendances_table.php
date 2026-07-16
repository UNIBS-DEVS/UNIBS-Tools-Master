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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('attendance_location_id')
                ->constrained('attendance_locations')
                ->cascadeOnDelete();

            $table->date('attendance_date');

            $table->dateTime('punch_at');

            $table->enum('punch_type', [
                'in',
                'out'
            ]);

            $table->enum('punch_source', [
                'Mobile',
                'Web',
                'Biometric',
                'Manual',
                'Others'
            ])->default('Mobile');

            $table->enum('status', [
                'auto_approved',
                'pending',
                'approved',
                'rejected'
            ])->default('pending');

            $table->text('remarks')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['user_id', 'attendance_date']);
            $table->index(['attendance_location_id']);
            $table->index(['punch_type']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
