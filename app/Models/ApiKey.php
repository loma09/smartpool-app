<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $fillable = ['device_id', 'api_key', 'is_active', 'last_used_at'];
    protected $casts    = ['is_active' => 'boolean', 'last_used_at' => 'datetime'];
}
