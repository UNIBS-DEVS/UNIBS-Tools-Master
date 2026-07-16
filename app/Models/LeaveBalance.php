<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    // protected $table = 'leave_balance';

    protected $fillable = [
        'leave_type_id',
        'user_id',
        'balance',
        'created_by',
        'updated_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Belongs to LeaveType
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    // Belongs to User (Owner)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
