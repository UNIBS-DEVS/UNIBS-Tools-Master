<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_documents', function (Blueprint $table) {
            $table->bigIncrements('document_id');
            $table->foreignId('asset_id')->constrained('asset_masters')->cascadeOnDelete();
            $table->string('document_type', 50)->nullable();
            $table->string('file_name', 500);
            $table->string('file_path', 1000);
            $table->timestamp('uploaded_on')->useCurrent();
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
        Schema::dropIfExists('asset_documents');
    }
};