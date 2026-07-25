<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $table = 'applications';

    protected $fillable = [
        'appCode',
        'appName',
        'status',
        'status_message',
        'created_by',
        'updated_by'
    ];

    /**
     * One Application has many Modules
     */
    public function modules()
    {
        return $this->hasMany(Module::class, 'app_id', 'id');
    }

    // Module.php
    public function application()
    {
        return $this->belongsTo(Application::class, 'app_id', 'id');
    }
}
