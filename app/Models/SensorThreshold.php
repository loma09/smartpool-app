<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SensorThreshold extends Model
{
    protected $fillable = ['key', 'value', 'unit', 'description'];

    public static function get(string $key, float $default = 0): float
    {
        return (float) (self::where('key', $key)->value('value') ?? $default);
    }
}
