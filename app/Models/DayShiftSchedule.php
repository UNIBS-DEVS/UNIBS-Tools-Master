<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DayShiftSchedule extends Model
{
    protected $fillable = [
        'shift_schedule_id',
        'day',
        'start_time',
        'end_time',
        'grace_minutes',
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

    public function shiftSchedule()
    {
        return $this->belongsTo(
            ShiftSchedule::class,
            'shift_schedule_id'
        );
    }
}
