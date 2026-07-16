<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $table = 'leave_requests';
    protected $fillable = [
        'leave_type_id',
        'duration',
        'start_date',
        'end_date',
        'remarks',
        'status',
        'manager_remarks',
        'manager_action_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date'        => 'date',
        'end_date'          => 'date',
        'manager_action_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Belongs to LeaveType
    // public function leaveType()
    // {
    //     return $this->belongsTo(LeaveType::class);
    // }

    // Requested By User
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Updated By User
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function leaveType()
    {
        return $this->belongsTo(
            LeaveType::class,
            'leave_type_id'
        );
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
