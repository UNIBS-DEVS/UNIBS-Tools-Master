<?php

namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'position',
        'skill',
        'experience',
        'status',
        'budget',
        'location',
        'count',
        'jd_path',
        'remarks',
        'created_by',
        'updated_by',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function recruiterAssignments()
    {
        return $this->hasMany(
            RecruiterAssignment::class
        );
    }

    public function recruiters()
    {
        return $this->belongsToMany(
            User::class,
            'recruiter_assignments',
            'customer_job_id',
            'recruiter_id'
        );
    }
}
