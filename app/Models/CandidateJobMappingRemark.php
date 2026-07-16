<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateJobMappingRemark extends Model
{
    protected $fillable = [
        'candidate_id',
        'remark_type',
        'remarks',
        'created_by',
        'updated_by',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
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
