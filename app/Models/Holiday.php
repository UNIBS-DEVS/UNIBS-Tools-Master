<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'holiday_year',
        'holiday_date',
        'description',
        'holiday_type',
        'created_by',
        'updated_by'
    ];
}
