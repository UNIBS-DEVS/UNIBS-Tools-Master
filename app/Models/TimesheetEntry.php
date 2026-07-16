<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimesheetEntry extends Model
{
    protected $fillable = [
        'timesheet_id',
        'work_date',
        'sub_activity_id',
        'hours',
        'remarks',
        'customer_id',
        'request_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'work_date' => 'string',
    ];

    public function timesheet()
    {
        return $this->belongsTo(Timesheet::class);
    }

    public function subActivity()
    {
        return $this->belongsTo(SubActivity::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }



    // public function request()
    // {
    //     return $this->belongsTo(Request::class, 'request_id');
    // }


}
