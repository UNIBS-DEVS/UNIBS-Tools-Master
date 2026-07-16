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
        Schema::create('customer_jobs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->onDelete('cascade');

            $table->string('position');

            $table->string('skill');

            $table->string('experience')->nullable();

            $table->enum('status', ['Open', 'Closed', 'On-Hold'])
                ->default('Open');

            $table->string('budget')->nullable();

            $table->string('location')->nullable();

            $table->unsignedInteger('count')->default(1);

            $table->string('jd_path')->nullable();

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
        Schema::dropIfExists('customer_jobs');
    }
};
