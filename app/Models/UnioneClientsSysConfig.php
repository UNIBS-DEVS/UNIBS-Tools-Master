<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnioneClientsSysConfig extends Model
{
    protected $table = 'unione_clients_sys_config';

    protected $fillable = [
        'client_id',

        'support_user',
        'support_password',

        'hr_email',
        'accounts_email',

        'attendance_notification_email',
        'timesheet_notification_email',
        'call_review_notification_email',

        'db_host',
        'db_mysql_port',
        'db_name',
        'db_username',
        'db_password',

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

        'modules'
    ];

    protected $casts = [
        'login_auth_type' => 'string',
        'email_auth_type' => 'string',
        'resume_parsing_time' => 'array', // ✅ important

        'modules' => 'array',
    ];

    /**
     * Relationship with Client (if you have clients table)
     */
    public function unioneClientsMaster()
    {
        return $this->belongsTo(UnioneClientsMaster::class, 'client_id');
    }
}
