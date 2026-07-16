<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CjmChange extends Model
{
    protected $fillable = [
        'candidate_id',
        'changed_field',
        'old_value',
        'new_value',
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

    public function changeHistories()
    {
        return $this->hasMany(
            CjmChange::class,
            'candidate_id'
        );
    }
}
