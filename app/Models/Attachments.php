<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachments extends Model
{
    protected $table = 'attachments';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'expense_item_id',
        'attachment_name',
        'attachment_path',
        'uploaded_at',
        'created_by',   
        'updated_by'
    ];

public function item()
{
    return $this->belongsTo(
        ExpenseItem::class,
        'expense_item_id'
    );
}

public function creator()
{
    return $this->belongsTo(
        User::class,
        'created_by'
    );
}
public function expenseItem()
{
    return $this->belongsTo(
        ExpenseItem::class,
        'expense_item_id'
    );
}
}