<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveTransaction extends Model
{
    protected $table = 'leave_transactions';

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'transaction_type',
        'amount',
        'previous_balance',
        'current_balance',
        'remarks',
        'created_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Belongs to User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Belongs to LeaveType
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    // Created By User
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
