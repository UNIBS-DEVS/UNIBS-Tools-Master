<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [

        'client_contact',
        'company',
        'email',
        'mobile',
        'location',
        'requirement',
        'type',
        'source',
        'follow_up_date',
        'status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function remarkHistories()
    {
        return $this->hasMany(SaleRemark::class)
            ->latest();
    }
}
