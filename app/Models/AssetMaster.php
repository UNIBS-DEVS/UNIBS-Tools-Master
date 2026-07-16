<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetMaster extends Model
{
    protected $table = 'asset_masters';

    protected $fillable = [
        'asset_category',
        'asset_code',
        'asset_name',
        'serial_number',
        'brand_name',
        'model_number',
        'vendor_id',
        'purchase_date',
        'purchase_cost',
        'status',
        'warranty_expiry_date',
        'created_by',
        'updated_by'
    ];

    protected $appends = [
        'AssetMaster_category',
        'AssetMaster_number',
        'asset_number',
        'vendor'
    ];

    public function getAssetMasterCategoryAttribute()
    {
        return $this->attributes['asset_category'] ?? null;
    }

    public function setAssetMasterCategoryAttribute($value)
    {
        $this->attributes['asset_category'] = $value;
    }

    public function getAssetMasterNumberAttribute()
    {
        return $this->attributes['asset_code'] ?? null;
    }

    public function setAssetMasterNumberAttribute($value)
    {
        $this->attributes['asset_code'] = $value;
    }

    public function getAssetNumberAttribute()
    {
        return $this->attributes['asset_code'] ?? null;
    }

    public function setAssetNumberAttribute($value)
    {
        $this->attributes['asset_code'] = $value;
    }

    public function getVendorAttribute()
    {
        return $this->attributes['vendor_id'] ?? null;
    }

    public function setVendorAttribute($value)
    {
        $this->attributes['vendor_id'] = $value;
    }
    public function getStatusAttribute($value)
    {
        if (empty($value)) {
            return 'Available';
        }
        $normalized = str_replace('-', ' ', strtolower($value));
        return ucwords($normalized);
    }

    public function vendorRelation()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function allocations()
    {
        return $this->hasMany(AssetAllocation::class, 'asset_id');
    }

    public function currentAllocation()
    {
        return $this->hasOne(AssetAllocation::class, 'asset_id')
            ->where('status', 'Allocated')
            ->latestOfMany();
    }

    public function repairs()
    {
        return $this->hasMany(AssetRepair::class, 'asset_id');
    }

    public function currentRepair()
    {
        return $this->hasOne(AssetRepair::class, 'asset_id')
            ->whereIn('repair_status', ['Reported', 'Sent for Repair', 'Under Repair'])
            ->latestOfMany();
    }

    public function recharges()
    {
        return $this->hasMany(SIMRecharge::class, 'asset_id');
    }

    public function latestRecharge()
    {
        return $this->hasOne(SIMRecharge::class, 'asset_id')->latestOfMany('recharge_date');
    }

    public function documents()
    {
        return $this->hasMany(AssetDocument::class, 'asset_id');
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