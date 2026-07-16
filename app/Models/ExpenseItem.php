<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseItem extends Model
{
    protected $table = 'expense_items';

    protected $fillable = [
        'expense_id',
        'category_id',
        'amount',
        'expense_date',
        'created_by',
        'updated_by',
        'expense_reason',
    ];

    public function expense()
    {
        return $this->belongsTo(
            Expense::class,
            'expense_id'
        );
    }

    public function category()
    {
        return $this->belongsTo(
            ExpenseCategory::class,
            'category_id'
        );
    }
    public function attachments()
{
    return $this->hasMany(
        Attachments::class,
        'expense_item_id'
    );
}
}