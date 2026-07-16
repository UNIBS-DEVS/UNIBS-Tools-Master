<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLocation extends Model
{
    protected $fillable = [
        'location_name',
        'type',
        'latitude',
        'longitude',
        'radius',
        'is_active',
        'shift_schedule_id',
        'created_by',
        'updated_by',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function userLocations()
    {
        return $this->hasMany(UserAttendanceLocation::class);
    }

    public function shiftSchedule()
    {
        return $this->belongsTo(
            ShiftSchedule::class,
            'shift_schedule_id'
        );
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_attendance_locations'
        )->withPivot([
            'status',
            'created_by',
            'updated_by',
        ])->withTimestamps();
    }
}
