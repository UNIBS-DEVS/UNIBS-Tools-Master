<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'modules';

    protected $fillable = [
        'app_id',
        'name',
        'created_by',
        'updated_by',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class, 'app_id');
    }

    public function clientModules()
    {
        return $this->hasMany(ClientModule::class, 'module_id');
    }
}
