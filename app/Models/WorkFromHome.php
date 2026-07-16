<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkFromHome extends Model
{
    protected $table = 'work_from_home';

    protected $fillable = [
        'user_id',
        'date',
        'reason',
        'type',
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
