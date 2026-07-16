<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetAllocation extends Model
{
    protected $table = 'asset_allocations';

    protected $fillable = [
        'asset_id',
        'employee_id',
        'allocated_date',
        'returned_date',
        'end_date',
        'status',
        'remarks',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'allocated_date' => 'datetime',
        'returned_date'  => 'datetime',
    ];

    /**
     * Get the asset that is allocated.
     */
    public function asset()
    {
        return $this->belongsTo(AssetMaster::class, 'asset_id');
    }

    /**
     * Get the employee (user) to whom the asset is allocated.
     */
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
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