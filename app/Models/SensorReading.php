<?php
// app/Models/SensorReading.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    protected $fillable = [
        'device_id', 'turbidity_value', 'turbidity_status',
        'rain_detected', 'rain_value', 'esp32_online',
    ];

    protected $casts = [
        'rain_detected' => 'boolean',
        'esp32_online'  => 'boolean',
    ];

    /** Ambil data terbaru per device */
    public static function latestByDevice(string $deviceId): ?self
    {
        return self::where('device_id', $deviceId)->latest()->first();
    }

    public function getTurbidityLabelAttribute(): string
    {
        return match ($this->turbidity_status) {
            'jernih'      => 'Jernih',
            'keruh'       => 'Keruh',
            'sangat_keruh' => 'Sangat Keruh',
            default       => '-',
        };
    }

    public function getTurbidityColorAttribute(): string
    {
        return match ($this->turbidity_status) {
            'jernih'      => 'success',
            'keruh'       => 'warning',
            'sangat_keruh' => 'danger',
            default       => 'secondary',
        };
    }
}
