<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'expenses';

    protected $fillable = [
        'employee_id',
        'user_remarks',
        'status',
        'manager_action_at',
        'manager_remarks',
        'created_by',
        'updated_by'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function employee()
    {
        return $this->belongsTo(
            User::class,
            'employee_id'
        );
    }

    public function items()
    {
        return $this->hasMany(
            ExpenseItem::class,
            'expense_id',
            'id'
        );
    }

    // public function attachments()
    // {
    //     return $this->hasMany(
    //         Attachments::class,
    //         'expense_id',
    //         'id'
    //     );
    // }

    // public function attachment()
    // {
    //     return $this->hasOne(
    //         Attachments::class,
    //         'expense_id',
    //         'id'
    //     );
    // }

    public function reimbursement()
    {
        return $this->hasOne(
            Reimbursement::class,
            'expense_id',
            'id'
        );
    }

    public function advanceSettlements()
    {
        return $this->hasMany(
            AdvanceSettlement::class,
            'expense_id',
            'id'
        );
    }

    public function advanceRequests()
    {
        return $this->belongsToMany(
            AdvanceRequest::class,
            'advance_settlements',
            'expense_id',
            'advance_id'
        )->withPivot('settled_amount', 'remarks', 'created_at', 'updated_at');
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }

    

    public function getTotalAmountAttribute()
    {
        return $this->items->sum('amount');
    }

    public function getAmountAttribute()
    {
        return $this->total_amount;
    }

    public function getExpenseDateAttribute()
    {
        return $this->created_at ? $this->created_at->format('d/M/Y') : '-';
    }

    public function getExpenseReasonAttribute()
    {
        return $this->user_remarks;
    }

    public function getTotalItemsAttribute()
    {
        return $this->items->count();
    }


}