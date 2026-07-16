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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->string('customer');
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();

            $table->enum('status', [
                'Active',
                'Inactive',
                'On-Hold',
                'Blacklisted'
            ])->default('Active');

            $table->enum('domain', [
                'IT',
                'Non-IT'
            ])->default('IT');

            $table->string('spoc')->nullable();
            $table->string('backup_spoc')->nullable();

            $table->text('remarks')->nullable();

            // Created / Updated By
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
        Schema::dropIfExists('customers');
    }
};
