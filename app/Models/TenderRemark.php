<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderRemark extends Model
{
    protected $fillable = [
        'tender_id',
        'remarks',
        'created_by',
        'updated_by',
    ];

    /* ---------------- Relationships ---------------- */

    public function tender()
    {
        return $this->belongsTo(Tender::class);
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
