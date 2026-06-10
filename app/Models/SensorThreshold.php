<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SensorThreshold extends Model
{
    protected $fillable = ['device_id', 'key', 'value', 'unit', 'description'];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    // Ambil threshold per device, fallback ke global jika tidak ada
    public static function get(string $key, float $default = 0, ?int $deviceId = null): float
    {
        // Cari per device dulu
        if ($deviceId) {
            $value = self::where('key', $key)
                ->where('device_id', $deviceId)
                ->value('value');
            if ($value !== null) return (float) $value;
        }

        // Fallback ke global (device_id null)
        return (float) (self::where('key', $key)
            ->whereNull('device_id')
            ->value('value') ?? $default);
    }
}