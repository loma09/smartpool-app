<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = ['user_id', 'device_id', 'name', 'location', 'is_active', 'last_seen_at'];

    protected $casts = [
        'is_active'    => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sensorReadings()
    {
        return $this->hasMany(SensorReading::class, 'device_id', 'id');
    }

    public function rainLogs()
    {
        return $this->hasMany(RainLog::class, 'device_id', 'id');
    }

    public function chlorineLogs()
    {
        return $this->hasMany(ChlorineLog::class, 'device_id', 'id');
    }

    public function latestReading()
    {
        return $this->hasOne(SensorReading::class, 'device_id', 'id')->latest();
    }

    public function isOnline(): bool
    {
        $latest = $this->latestReading()->first();
        if (!$latest) return false;
        return $latest->created_at->diffInMinutes(now()) < 2;
    }
    public function apiKey()
    {
        return $this->hasOne(\App\Models\ApiKey::class, 'device_id', 'id');
    }
}