<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Timesheet extends Model
{
    protected $fillable = [
        'user_id',
        'week_start',
        'week_end',
        'total_hours',
        'user_remarks',
        'status',
        'user_submission_at',
        'manager_id',
        'manager_action_at',
        'manager_remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'week_start'         => 'date',
        'week_end'           => 'date',
        'total_hours'        => 'decimal:2',
        'user_submission_at' => 'datetime',
        'manager_action_at'  => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function entries()
    {
        return $this->hasMany(TimesheetEntry::class);
    }

    public function getWeekRangeAttribute()
    {
        return Carbon::parse($this->week_start)->format('d M, Y')
            . ' → ' .
            Carbon::parse($this->week_end)->format('d M, Y');
    }

    public function getWeekStartFormattedAttribute()
    {
        return Carbon::parse($this->week_start)->format('Y-m-d');
    }
}
