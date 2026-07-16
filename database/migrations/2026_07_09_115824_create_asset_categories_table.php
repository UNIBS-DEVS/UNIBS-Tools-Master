<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->increments('id');

            $table->string('category_name', 50);

            $table->enum('status', [
                'active',
                'inactive',
            ]);

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
        Schema::dropIfExists('asset_categories');
    }
};