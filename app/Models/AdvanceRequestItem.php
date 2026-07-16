<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvanceRequestItem extends Model
{
    use HasFactory;

    protected $table = 'advance_request_items';

    protected $fillable = [
        'advance_req_id',
        'category_id',
        'requested_amount',
        'expense_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'requested_amount' => 'decimal:2',
    ];

    /**
     * Parent Advance Request
     */
    public function advanceRequest()
    {
        return $this->belongsTo(
            AdvanceRequest::class,
            'advance_req_id'
        );
    }

    /**
     * Expense Category
     */
    public function category()
    {
        return $this->belongsTo(
            ExpenseCategory::class,
            'category_id'
        );
    }

    /**
     * User who created record
     */
    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /**
     * User who last updated record
     */
    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }

    /**
     * Scope: Filter by category
     */
    public function scopeCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateBetween($query, $from, $to)
    {
        return $query->whereBetween(
            'created_at',
            [$from, $to]
        );
    }
}