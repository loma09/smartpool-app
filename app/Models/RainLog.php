<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RainLog extends Model
{
    protected $fillable = ['device_id', 'rain_value', 'cover_closed', 'notes'];

    protected $casts = ['cover_closed' => 'boolean'];
}
