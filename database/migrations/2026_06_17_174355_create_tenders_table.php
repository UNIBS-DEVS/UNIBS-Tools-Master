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
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();

            $table->string('tender_num');

            $table->foreignId('primary_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('secondary_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('submission_date')->nullable();

            $table->enum('type', [
                'IT Manpower',
                'Non-IT Manpower',
                'SAP',
                'Trainings',
                'IT Projects',
                'Others'
            ])->default('Others');

            $table->enum('status', [
                'Pending',
                'Submitted',
                'Under Evaluation',
                'Won',
                'Lost',
            ])->default('Pending');

            $table->date('due_date')->nullable();

            $table->string('estimated_value')->nullable(); // INR Lakhs

            $table->string('state')->nullable();

            $table->string('department')->nullable();

            $table->string('bid_price')->nullable();

            $table->enum('platform', [
                'GeM',
                'CPPP',
                'IREPS',
                'State eProcurement Portals',
                'NHAI',
                'NTPC',
                'ONGC',
                'BHEL',
                'OTHERS'
            ])->default('GeM');

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

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
        Schema::dropIfExists('tenders');
    }
};
