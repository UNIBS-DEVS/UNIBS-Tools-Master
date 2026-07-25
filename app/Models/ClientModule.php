<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientModule extends Model
{
    protected $table = 'client_modules';

    protected $fillable = [
        'client_id',
        'app_id',
        'module_id',
        'created_by',
        'updated_by',
    ];

    public function client()
    {
        return $this->belongsTo(UnioneClientsMaster::class, 'client_id');
    }

    public function application()
    {
        return $this->belongsTo(Application::class, 'app_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }
}
