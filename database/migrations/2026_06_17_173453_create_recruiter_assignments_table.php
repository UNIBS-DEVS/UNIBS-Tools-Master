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
        Schema::create('recruiter_assignments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('customer_job_id')
                ->constrained('customer_jobs')
                ->cascadeOnDelete();

            $table->foreignId('recruiter_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('assignment_date');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                [
                    'customer_job_id',
                    'recruiter_id',
                    'assignment_date'
                ],
                'rec_assign_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruiter_assignments');
    }
};
