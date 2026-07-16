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
        Schema::create('advance_requests', function (Blueprint $table) {

            $table->id();

            $table->foreignId('users_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('advance_reason');

            $table->decimal('approved_amount', 12, 2)
                ->default(0);

            $table->enum('status', [
                'Pending',  
                'Submitted',
                'Approved',
                'Paid',
                'Partially Settled',
                'Fully Settled'
            ])->default('Pending');

            $table->decimal('pending_amount', 12, 2)
                ->default(0);

            $table->timestamp('manager_action_at')
                ->nullable();

            $table->text('manager_remarks')
                ->nullable();

            // Added from the second migration
            $table->text('accounts_remarks')
                ->nullable();
 
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
        Schema::dropIfExists('advance_requests');
    }
};