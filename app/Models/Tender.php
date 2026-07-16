<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    protected $fillable = [
        'tender_num',
        'primary_user_id',
        'secondary_user_id',
        'submission_date',
        'type',
        'status',
        'due_date',
        'estimated_value',
        'state',
        'department',
        'bid_price',
        'platform',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'submission_date' => 'date',
        'due_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function primaryUser()
    {
        return $this->belongsTo(User::class, 'primary_user_id');
    }

    public function secondaryUser()
    {
        return $this->belongsTo(User::class, 'secondary_user_id');
    }

    public function remarkHistories()
    {
        return $this->hasMany(TenderRemark::class)
            ->latest();
    }
}
