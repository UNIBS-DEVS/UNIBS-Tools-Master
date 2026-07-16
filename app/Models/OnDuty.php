<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnDuty extends Model
{
    protected $table = 'on_duty';

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'reason',
        'status',
        'manager_remarks',
        'manager_action_at',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'manager_action_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }
}
