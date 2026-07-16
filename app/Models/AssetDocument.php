<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetDocument extends Model
{
    protected $table = 'asset_documents';

    protected $primaryKey = 'document_id';

    protected $fillable = [
        'asset_id',
        'document_type',
        'file_name',
        'file_path',
        'uploaded_on',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'uploaded_on' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(AssetMaster::class, 'asset_id');
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