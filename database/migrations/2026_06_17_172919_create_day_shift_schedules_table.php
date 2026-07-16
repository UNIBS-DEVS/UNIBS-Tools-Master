<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('day_shift_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shift_schedule_id')
                ->constrained('shift_schedules')
                ->cascadeOnDelete();

            $table->string('day', 20);

            $table->time('start_time');
            $table->time('end_time');

            $table->integer('grace_minutes')->default(0);

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

    public function down(): void
    {
        Schema::dropIfExists('day_shift_schedules');
    }
};
