<?php

namespace App\Models;

use App\Models\CustomerJob;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'customer',
        'contact',
        'email',
        'mobile',
        'status',
        'domain',
        'spoc',
        'backup_spoc',
        'remarks',
        'created_by',
        'updated_by',
    ];

    public function jobs()
    {
        return $this->hasMany(CustomerJob::class);
    }

    public function spocUser()
    {
        return $this->belongsTo(User::class, 'spoc');
    }

    public function backupSpoc()
    {
        return $this->belongsTo(User::class, 'backup_spoc');
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
