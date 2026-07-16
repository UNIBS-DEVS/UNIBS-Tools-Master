<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Review extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'email',
        'from_number',
        'to_number',
        'to_name',
        'call_date',
        'call_time',
        'duration',
        'requirement_type',
        'type',

        // new file-related columns
        'recording_path',
        'recording_name',
        'original_name',
        'mime_type',
        'size_bytes',

        'user_id',
        'added_by',
        'updated_by',
    ];

    // Employee (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function notes()
    {
        return $this->hasMany(ReviewNotes::class, 'review_id');
    }

    public function userLatestNote()
    {
        $query = $this->hasOne(ReviewNotes::class, 'review_id');

        // Non-admin users see only their own latest note
        if (!Auth::user()?->hasRole('admin')) {
            $query->where('user_id', Auth::id());
        }

        return $query->latestOfMany('created_at');
    }

    public function userOldestNote()
    {
        $query = $this->hasOne(ReviewNotes::class, 'review_id');

        // Non-admin users see only their own oldest note
        if (!Auth::user()?->hasRole('admin')) {
            $query->where('user_id', Auth::id());
        }

        return $query->oldestOfMany('created_at');
    }

    // public function contactUser()
    // {
    //     return $this->belongsTo(User::class, 'from_number', 'mobile')
    //         ->select(['id', 'name', 'roles', 'mobile']); // include mobile!
    // }

    // public function personalContact()
    // {
    //     return $this->belongsTo(User::class, 'from_number', 'personal_mobile');
    // }

    // public function officialContact()
    // {
    //     return $this->belongsTo(User::class, 'from_number', 'offical_mobile');
    // }

    // public function userOldestNote()
    // {
    //     return $this->hasOne(ReviewNotes::class, 'review_id')
    //         ->where('user_id', Auth::id())
    //         ->oldestOfMany('created_at'); // explicitly based on created_at
    // }

    // public function userLatestNote()
    // {
    //     return $this->hasOne(ReviewNotes::class, 'review_id')
    //         ->where('user_id', Auth::id())
    //         ->latestOfMany('created_at');
    // }

    /**
     * Latest note logic
     * User -> own latest note
     * Admin -> latest note from any user
     */

    /**
     * Oldest note logic
     * User -> own oldest note
     * Admin -> oldest note from any user
     */
}