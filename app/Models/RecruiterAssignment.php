<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecruiterAssignment extends Model
{
    protected $fillable = [
        'customer_job_id',
        'recruiter_id',
        'assignment_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'assignment_date' => 'date',
    ];

    public function customerJob()
    {
        return $this->belongsTo(CustomerJob::class);
    }

    public function recruiter()
    {
        return $this->belongsTo(User::class, 'recruiter_id');
    }
}
