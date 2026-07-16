<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallReviewSetting extends Model
{
    protected $fillable = [
        'setting_parameter',
        'setting_value',
        'remarks',
        'user_id',
        'updated_by_user'
    ];

    // (Relationships)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function update_by_user()
    {
        return $this->belongsTo(User::class, 'updated_by_user');
    }
}
