<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $fillable = ['device_id', 'api_key', 'is_active', 'last_used_at'];
    protected $casts    = ['is_active' => 'boolean', 'last_used_at' => 'datetime'];

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }
}
