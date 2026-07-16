<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {

            $table->id();

            $table->foreignId('employee_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('user_remarks');

            $table->enum('status', [
                'Pending',
                'Submitted',
                'Approved',
                'Rejected',
                'Reimbursed'
            ])->default('Pending');

            $table->timestamp('manager_action_at')
                ->nullable();

            $table->text('manager_remarks')
                ->nullable();

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
        Schema::dropIfExists('expenses');
    }
};