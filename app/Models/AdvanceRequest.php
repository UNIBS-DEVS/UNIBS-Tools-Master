<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvanceRequest extends Model
{
    use HasFactory;

    protected $table = 'advance_requests';

    protected $fillable = [
        'users_id',
        'advance_reason',
        'approved_amount',
        'status',
        'pending_amount',
        'manager_action_at',
        'manager_remarks',
        'accounts_remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'approved_amount'   => 'decimal:2',
        'pending_amount'    => 'decimal:2',
        'manager_action_at' => 'datetime',
    ];

    /**
     * Employee / user who created the advance request
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    /**
     * All items included in this advance request
     */
    public function items()
    {
        return $this->hasMany(AdvanceRequestItem::class, 'advance_req_id');
    }

    /**
     * All payments made against this advance request
     */
    public function payments()
    {
        return $this->hasMany(AdvancePayment::class, 'advance_id');
    }

    /**
     * Total requested amount from all items
     */
    public function getTotalRequestedAmountAttribute()
    {
        return $this->items->sum('requested_amount');
    }

    /**
     * Total paid amount from all payments
     */
    public function getTotalPaidAmountAttribute()
    {
        return $this->payments->sum('paid_amount');
    }

    /**
     * Remaining pending amount
     */
    public function getBalanceAmountAttribute()
    {
        return (float) ($this->approved_amount ?? 0) - (float) $this->total_paid_amount;
    }

    /**
     * Scope: Pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope: Submitted requests
     */
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'Submitted');
    }

    /**
     * Scope: Approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    /**
     * Scope: Paid requests
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'Paid');
    }

    /**
     * Scope: Partially settled requests
     */
    public function scopePartiallySettled($query)
    {
        return $query->where('status', 'Partially Settled');
    }

    /**
     * Scope: Fully settled requests
     */
    public function scopeFullySettled($query)
    {
        return $query->where('status', 'Fully Settled');
    }
}