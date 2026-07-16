<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'asset_category',
        'asset_number',
        'serial_number',
        'brand_name',
        'model_number',
        'vendor',
        'purchase_type',
        'quantity',
        'allocated_to',   // correct
        'allocation_date',
        'type',
        'sim_name',
        'item',
        'status'
    ];
}
