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
        Schema::create('reimbursements', function (Blueprint $table) {

            $table->id();

            $table->foreignId('expense_id')
                ->constrained('expenses')
                ->cascadeOnDelete();

            $table->decimal('amount_paid', 10, 2);

            $table->date('payment_date')->nullable();

            $table->string('payment_method')->nullable();
            // Cash, Bank Transfer, UPI, Cheque, UPI, etc.

            $table->string('transaction_reference')->nullable();

            $table->enum('status', [
                'Pending',
                'Paid',
                'Failed'
            ])->default('Pending');

            $table->text('accounts_remarks')->nullable();

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
        Schema::dropIfExists('reimbursements');
    }
};
