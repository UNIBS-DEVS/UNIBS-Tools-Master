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
        Schema::create('asset_repairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('asset_masters')->cascadeOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->string('issue_description', 1000)->nullable();
            $table->dateTime('reported_date')->nullable();
            $table->dateTime('sent_date')->nullable();
            $table->dateTime('received_date')->nullable();
            $table->decimal('repair_cost', 18, 2)->nullable();
            $table->string('repair_status', 50)->default('Reported');
            $table->string('remarks', 500)->nullable();
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_repairs');
    }
};