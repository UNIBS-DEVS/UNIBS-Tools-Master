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
        Schema::create('attendance_punch_audit', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->string('device_id', 255);

            $table->dateTime('device_time')->nullable();

            $table->string('action', 20)->nullable();

            $table->boolean('punch_success')->nullable();

            $table->string('skip_reason', 100)->nullable();

            $table->boolean('is_checked_in')->nullable();

            $table->unsignedBigInteger('active_location_id')->nullable();
            $table->unsignedBigInteger('eligible_location_id')->nullable();

            $table->boolean('token_found')->nullable();
            $table->boolean('local_state_used')->nullable();
            $table->boolean('server_state_fetched')->nullable();

            $table->text('position_error')->nullable();
            $table->text('gps_error')->nullable();
            $table->text('locations_api_error')->nullable();

            $table->decimal('position_lat', 10, 7)->nullable();
            $table->decimal('position_lng', 10, 7)->nullable();
            $table->decimal('position_accuracy_m', 8, 2)->nullable();

            $table->string('position_source', 50)->nullable();

            $table->text('exception')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
            $table->index('device_time');
            $table->index('action');
            $table->index('punch_success');
            $table->index('active_location_id');
            $table->index('eligible_location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_punch_audit');
    }
};
