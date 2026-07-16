<?php

namespace App\Models;

use App\Models\LeaveRequest;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'leave_name',
        'accrual_type', 
        'accrual',
        'max_balance',
        'status',
        'created_by',
        'updated_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // One LeaveType → Many LeaveRequests
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    // One LeaveType → Many LeaveBalances
    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    // Created By User
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Updated By User
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
