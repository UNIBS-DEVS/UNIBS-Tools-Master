<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadMobileApp extends Model
{
    protected $table = 'upload_mobile_apps';

    protected $fillable = [
        'application',
        'version_name',
        'version_code',
        'force_update',
        'apk_url',
        'update_message',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'force_update' => 'boolean'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
