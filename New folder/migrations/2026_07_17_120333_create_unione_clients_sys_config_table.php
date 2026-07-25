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
        Schema::create('unione_clients_sys_config', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->unique()
                ->constrained('unione_clients_master')
                ->cascadeOnDelete();

            // Support
            $table->string('support_user')->nullable();
            $table->string('support_password')->nullable();

            // Emails
            $table->string('hr_email')->nullable();
            $table->string('accounts_email')->nullable();
            $table->string('attendance_notification_email')->nullable();
            $table->string('timesheet_notification_email')->nullable();
            $table->string('call_review_notification_email')->nullable();

            // Database
            $table->string('db_host');
            $table->unsignedSmallInteger('db_mysql_port')->default(3306);
            $table->string('db_name');
            $table->string('db_username');
            $table->string('db_password')->nullable();

            // SMTP
            $table->string('smtp_host')->nullable();
            $table->unsignedSmallInteger('smtp_port')->nullable();
            $table->enum('smtp_auth', ['tls', 'ssl'])->nullable();

            // Microsoft Graph
            $table->string('graph_client_id')->nullable();
            $table->string('graph_tenant_id')->nullable();
            $table->string('graph_client_secret_id')->nullable();
            $table->text('graph_client_secret_value')->nullable();
            $table->string('graph_redirect_url')->nullable();
            $table->date('graph_client_expiry_date')->nullable();

            // Authentication
            $table->enum('login_auth_type', ['basic', 'oauth'])->default('basic');
            $table->enum('email_auth_type', ['smtp', 'graph_id'])->default('smtp');

            // Multiple roles (JSON)
            $table->json('modules')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unione_clients_sys_config');
    }
};
