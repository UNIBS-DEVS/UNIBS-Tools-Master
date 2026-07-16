<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sim_recharges', function (Blueprint $table) {
            $table->bigIncrements('recharge_id');
            $table->foreignId('asset_id')->constrained('asset_masters')->cascadeOnDelete();
            $table->date('recharge_date');
            $table->string('plan_name', 100)->nullable();
            $table->decimal('recharge_amount', 18, 2)->nullable();
            $table->integer('validity_days')->nullable();
            $table->date('expiry_date')->nullable();
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

    public function down(): void
    {
        Schema::dropIfExists('sim_recharges');
    }
};