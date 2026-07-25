<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequirementType extends Model
{
    protected $fillable = [
        'name',
        'app_id',
        'client_id',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
