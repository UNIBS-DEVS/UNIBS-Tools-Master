<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        'customer_id',
        'customer_job_id',
        'candidate_name',
        'mobile',
        'email',
        'gender',
        'current_company',
        'skill',
        'notice_period',
        'last_working_day',
        'experience_years',
        'experience_months',
        'relevant_experience_years',
        'relevant_experience_months',
        'current_location',
        'preferred_location',
        'current_fixed_ctc',
        'current_variable_ctc',
        'expected_ctc',
        'status',
        'interview_date',
        'interview_level',
        'resume_path',
        'education',
        'remarks',
        'created_by',
        'updated_by',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function position()
    {
        return $this->belongsTo(CustomerJob::class, 'customer_job_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function customerJob()
    {
        return $this->belongsTo(CustomerJob::class);
    }

    public function remarkHistories()
    {
        return $this->hasMany(
            CandidateJobMappingRemark::class,
            'candidate_id'
        );
    }

    public function latestRemark()
    {
        return $this->hasOne(CandidateJobMappingRemark::class, 'candidate_id')->latestOfMany();
    }
}
