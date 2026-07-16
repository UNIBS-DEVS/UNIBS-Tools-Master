<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table = 'vendors';

    protected $fillable = [
        'vendor_name',
        'contact_person',
        'email',
        'mobile_no',
        'gst',
        'created_by',
        'updated_by'
    ];
}
