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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // Call Info
            $table->string('email')->nullable();
            $table->string('from_number')->nullable();
            $table->string('to_number')->nullable();

            $table->date('call_date')->nullable();
            $table->time('call_time')->nullable();

            $table->integer('duration')->nullable()->comment('Duration in seconds');

            $table->enum('requirement_type', [
                'sourcing',
                'training',
                'job seeker',
                'microsoft',
                'tally',
                'google',
                'zoho',
                'software services',
                'others'
            ])->default('others');

            $table->enum('type', ['incoming', 'outgoing', 'missed', 'rejected', 'voicemail', 'blocked', 'answered_externally'])->nullable();

            // $table->string('recording')->nullable();

            $table->string('recording_path')->nullable();     // recordings/2026/03/file.mp3
            $table->string('recording_name')->nullable();     // system generated
            $table->string('original_name')->nullable();      // original file name
            $table->string('mime_type')->nullable();          // audio/mpeg
            $table->integer('size_bytes')->nullable();        // file size

            // $table->text('notes')->nullable();

            // 🔹 Employee (User)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // 🔹 Audit Fields
            $table->foreignId('added_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // 🔹 Indexes (for filters)
            $table->index(['call_date']);
            $table->index(['type']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
