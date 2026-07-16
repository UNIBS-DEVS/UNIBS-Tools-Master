<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompOff extends Model
{
    protected $table = 'comp_off';

    protected $fillable = [
        'user_id',
        'day_worked',
        'reason',
        'status',
        'manager_remarks',
        'manager_action_at',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
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