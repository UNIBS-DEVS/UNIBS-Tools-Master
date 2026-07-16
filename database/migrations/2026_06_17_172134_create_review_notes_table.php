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
        Schema::create('review_notes', function (Blueprint $table) {
            $table->id();

            // link to reviews table
            $table->foreignId('review_id')
                ->constrained('reviews')
                ->cascadeOnDelete();

            // note text
            $table->text('note');

            // optional: who updated
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_notes');
    }
};
