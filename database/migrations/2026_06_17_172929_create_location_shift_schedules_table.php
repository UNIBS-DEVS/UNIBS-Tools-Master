<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location_shift_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('location_id')
                ->constrained('attendance_locations')
                ->cascadeOnDelete();

            $table->foreignId('shift_schedule_id')
                ->constrained('shift_schedules')
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['location_id', 'shift_schedule_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_shift_schedules');
    }
};
