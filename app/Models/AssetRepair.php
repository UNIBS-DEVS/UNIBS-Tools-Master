<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetRepair extends Model
{
    protected $table = 'asset_repairs';

    protected $fillable = [
        'asset_id',
        'vendor_id',
        'issue_description',
        'reported_date',
        'sent_date',
        'received_date',
        'repair_cost',
        'repair_status',
        'remarks',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'reported_date' => 'datetime',
        'sent_date'     => 'datetime',
        'received_date' => 'datetime',
        'repair_cost'   => 'decimal:2',
    ];

    /**
     * Get the asset that is under repair.
     */
    public function asset()
    {
        return $this->belongsTo(AssetMaster::class, 'asset_id');
    }

    /**
     * Get the vendor performing the repair.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
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
