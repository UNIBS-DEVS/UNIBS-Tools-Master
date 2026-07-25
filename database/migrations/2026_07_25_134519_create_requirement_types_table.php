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
        Schema::create('requirement_types', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);

            // Application (LMS, ATS, Unione)
            $table->foreignId('app_id')
                ->constrained('applications')
                ->cascadeOnDelete();

            // Client ID from corresponding application client table
            $table->unsignedBigInteger('client_id');

            $table->boolean('status')
                ->default(true)
                ->comment('1 = active, 0 = inactive');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Indexes
            $table->index(['app_id', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirement_types');
    }
};
