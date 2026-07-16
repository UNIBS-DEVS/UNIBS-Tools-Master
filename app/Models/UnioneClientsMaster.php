<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnioneClientsMaster extends Model
{
    use HasFactory;

    protected $table = 'unione_clients_master';

    protected $fillable = [
        'client_code',
        'client_name',
        'client_ship_to_address',
        'client_bill_to_address',
        'client_gst',
        'client_pan',
        'client_spoc_name',
        'client_spoc_email',
        'client_spoc_mobile',
        'status',
        'logo_path',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Scope for active clients
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for inactive clients
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Get the system configuration for the client
     */
    public function unioneClientsSysConfig()
    {
        return $this->hasOne(UnioneClientsSysConfig::class, 'client_id');
    }
}
