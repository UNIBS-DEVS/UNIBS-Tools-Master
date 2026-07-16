<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingDayMinHour extends Model
{
    use HasFactory;

    protected $table = 'working_day_min_hours';

    protected $fillable = [
        'day',
        'minimum_hours',
    ];

    protected $casts = [
        'minimum_hours' => 'decimal:2',
    ];
}
