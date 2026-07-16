<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvancePayment extends Model
{
    use HasFactory;

    protected $table = 'advance_payments';

    protected $fillable = [
        'advance_id',
        'paid_amount',
        'payment_date',
        'payment_mode',
        'reference_no',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'paid_amount'  => 'decimal:2',
        'payment_date' => 'date',
    ];

    /**
     * Parent Advance Request
     */
    public function advanceRequest()
    {
        return $this->belongsTo(
            AdvanceRequest::class,
            'advance_id'
        );
    }

    /**
     * User who created payment
     */
    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /**
     * User who last updated payment
     */
    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }

    /**
     * Scope: Filter by payment mode
     */
    public function scopePaymentMode($query, $mode)
    {
        return $query->where('payment_mode', $mode);
    }

    /**
     * Scope: Filter payments between dates
     */
    public function scopeDateBetween($query, $from, $to)
    {
        return $query->whereBetween(
            'payment_date',
            [$from, $to]
        );
    }

    /**
     * Scope: Bank payments only
     */
    public function scopeBank($query)
    {
        return $query->where('payment_mode', 'Bank');
    }

    /**
     * Scope: Cash payments only
     */
    public function scopeCash($query)
    {
        return $query->where('payment_mode', 'Cash');
    }
}