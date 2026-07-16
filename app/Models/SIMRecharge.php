<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SIMRecharge extends Model
{
    protected $table = 'sim_recharges';

    protected $primaryKey = 'recharge_id';

    protected $fillable = [
        'asset_id',
        'recharge_date',
        'plan_name',
        'recharge_amount',
        'validity_days',
        'expiry_date',
        'remarks',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'recharge_date' => 'date',
        'expiry_date'   => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(AssetMaster::class, 'asset_id');
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
