<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewNotes extends Model
{
    protected $table = 'review_notes';

    protected $fillable = [
        'review_id',
        'note',
        'user_id', // ✅ MUST ADD THIS
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
