<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reimbursement extends Model
{
    protected $table = 'reimbursements';

    protected $primaryKey = 'id';

    public $timestamps = true;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'expense_id',
        'amount_paid',
        'payment_date',
        'payment_method',
        'transaction_reference',
        'accounts_remarks',
        'processed_by',
        'status',
        'created_by',
        'updated_by'
    ];
}