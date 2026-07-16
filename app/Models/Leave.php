<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = [
        'leave_type',
        'duration',
        'start_date',
        'end_date',
        'remarks'
    ];
}

