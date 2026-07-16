<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAttendanceLocation extends Model
{
    protected $table = 'user_attendance_locations';

    protected $fillable = [
        'user_id',
        'attendance_location_id',
        'status',
        'created_by',
        'updated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceLocation()
    {
        return $this->belongsTo(AttendanceLocation::class);
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
