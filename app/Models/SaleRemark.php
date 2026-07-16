<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleRemark extends Model
{
    protected $table = 'sales_remarks';

    protected $fillable = [
        'sale_id',
        'remarks',
        'created_by',
        'updated_by',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
