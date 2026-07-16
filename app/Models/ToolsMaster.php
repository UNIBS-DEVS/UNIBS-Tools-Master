<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolsMaster extends Model
{
    use HasFactory;

    protected $table = 'tools_master';

    protected $fillable = [
        'support_user',
        'support_password',

        'hr_email',
        'accounts_email',

        'attendance_notification_email',
        'timesheet_notification_email',
        'call_review_notification_email',

        'smtp_host',
        'smtp_port',
        'smtp_auth',

        'graph_client_id',
        'graph_tenant_id',
        'graph_client_secret_id',
        'graph_client_secret_value',
        'graph_redirect_url',
        'graph_client_expiry_date',

        'login_auth_type',
        'email_auth_type',
    ];

    protected $casts = [
        'login_auth_type' => 'string',
        'email_auth_type' => 'string',
        'resume_parsing_time' => 'array', // ✅ important
    ];
}
