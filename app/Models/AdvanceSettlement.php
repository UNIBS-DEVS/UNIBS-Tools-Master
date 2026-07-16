<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvanceSettlement extends Model
{
    use HasFactory;

    protected $table = 'advance_settlements';

    protected $fillable = [
        'advance_id',
        'expense_id',
        'settled_amount',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'settled_amount' => 'decimal:2',
    ];

    /**
     * Associated Advance Request
     */
    public function advanceRequest()
    {
        return $this->belongsTo(
            AdvanceRequest::class,
            'advance_id'
        );
    }

    /**
     * Associated Expense
     */
    public function expense()
    {
        return $this->belongsTo(
            Expense::class,
            'expense_id'
        );
    }

    /**
     * User who created the settlement
     */
    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /**
     * User who updated the settlement
     */
    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }
}
