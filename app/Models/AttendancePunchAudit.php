<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendancePunchAudit extends Model
{
    protected $table = 'attendance_punch_audit';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'device_id',
        'device_time',
        'action',
        'skip_reason',
        'punch_success',
        'is_checked_in',
        'active_location_id',
        'token_found',
        'local_state_used',
        'server_state_fetched',
        'position_error',
        'gps_error',
        'locations_api_error',
        'position_lat',
        'position_lng',
        'position_accuracy_m',
        'position_source',
        'eligible_location_id',
        'exception',
    ];

    protected $casts = [
        'device_time' => 'datetime',
        'punch_success' => 'boolean',
        'is_checked_in' => 'boolean',
        'token_found' => 'boolean',
        'local_state_used' => 'boolean',
        'server_state_fetched' => 'boolean',
    ];
}
