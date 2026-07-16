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
        Schema::create('upload_mobile_apps', function (Blueprint $table) {

            $table->id();

            $table->enum('application', [
                'attendance',
                'call review'
            ]);

            $table->string('version_name', 100);
            $table->string('version_code', 50);

            $table->unique(['application', 'version_name']);
            $table->unique(['application', 'version_code']);

            $table->boolean('force_update')
                ->default(true);

            $table->text('update_message')
                ->nullable();

            $table->string('apk_url')->nullable();

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
        Schema::dropIfExists('upload_mobile_apps');
    }
};
