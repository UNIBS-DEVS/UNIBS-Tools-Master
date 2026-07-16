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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->cascadeOnDelete();

            $table->foreignId('customer_job_id')
                ->constrained('customer_jobs')
                ->cascadeOnDelete();

            // Candidate Details
            $table->string('candidate_name');

            $table->string('mobile');

            $table->string('email')->nullable();

            $table->enum('gender', [
                'Male',
                'Female',
                'Other'
            ])->nullable();

            $table->string('current_company')->nullable();

            $table->string('skill')->nullable();

            // Notice Period
            $table->enum('notice_period', [
                'Immediate',
                'Serving Notice',
                'Under 15 Days',
                'Under 30 Days',
                'Under 60 Days',
                '60 Days and Above'
            ])->nullable();

            $table->date('last_working_day')->nullable();

            // Experience 
            $table->integer('experience_years')->default(0);
            $table->integer('experience_months')->default(0);
            $table->integer('relevant_experience_years')->default(0);
            $table->integer('relevant_experience_months')->default(0);

            // Location
            $table->string('current_location')->nullable();

            $table->string('preferred_location')->nullable();

            // CTC
            $table->decimal('current_fixed_ctc', 10, 2)->nullable();

            $table->decimal('current_variable_ctc', 10, 2)->nullable();

            $table->decimal('expected_ctc', 10, 2)->nullable();

            // Candidate Status
            $table->enum('status', [
                'Mapped',
                'Under Discussion',
                'Shared with Customer',
                'Under Interview',
                'Offered',
                'Joined',
                'Back Out',
                'Closed',
            ])->default('Mapped');

            // Interview
            $table->dateTime('interview_date')->nullable();

            $table->enum('interview_level', [
                'L1',
                'L2',
                'Manager',
                'C Level',
                'HR'
            ])->nullable();

            $table->text('resume_path')->nullable();

            $table->string('education')->nullable();

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
        Schema::dropIfExists('candidates');
    }
};
