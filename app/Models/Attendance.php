<?php

namespace App\Models;

use App\Models\AttendanceLocation;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'attendance_location_id',
        'attendance_date',
        'punch_at',
        'punch_type',
        'punch_source',
        'status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'punch_at' => 'datetime',
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
