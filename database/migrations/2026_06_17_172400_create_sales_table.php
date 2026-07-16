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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->string('client_contact')->nullable();
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();

            $table->string('location')->nullable();

            $table->text('requirement')->nullable();

            $table->enum('type', [
                'Sourcing',
                'Training',
                'Job Seeker',
                'Microsoft',
                'Tally',
                'Google',
                'Zoho',
                'Software Services',
                'Digital Marketing',
                'Others'
            ])->default('Sourcing');

            $table->enum('source', [
                'IndiaMart',
                'Justdial',
                'Linkedin',
                'Facebook',
                'Instagram',
                'Twitter',
                'References',
                'Others'
            ])->default('Others');

            $table->date('follow_up_date')->nullable();

            $table->enum('status', [
                'New',
                'Won',
                'Lost',
                'Under Discussion',
                'On-Hold'
            ])->default('New');

            $table->longText('remarks')->nullable();

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
        Schema::dropIfExists('sales');
    }
};
