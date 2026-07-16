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
        Schema::create('attendance_locations', function (Blueprint $table) {
            $table->id();

            $table->string('location_name');

            $table->enum('type', [
                'office',
                'home',
            ])->default('office');

            $table->decimal('latitude', 11, 8);
            $table->decimal('longitude', 11, 8);

            // Radius in meters
            $table->unsignedInteger('radius')->default(100);

            $table->boolean('is_active')->default(true);

            $table->foreignId('shift_schedule_id')
                ->nullable()
                ->constrained('shift_schedules')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

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
        Schema::dropIfExists('attendance_locations');
    }
};
