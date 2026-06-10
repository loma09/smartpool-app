<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\SensorReading;
use App\Models\RainLog;
use App\Models\ChlorineLog;
use App\Models\SensorThreshold;

class SensorController extends Controller
{
    /**
     * POST /api/sensor/data
     */
    public function store(Request $request)
    {
        $request->validate([
            'device_id'       => 'required|string',
            'turbidity_value' => 'required|numeric',
            'rain_value'      => 'required|integer',
            'esp32_online'    => 'required|boolean',
        ]);

        $device = Device::where('device_id', $request->device_id)
            ->where('is_active', true)
            ->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device tidak ditemukan atau tidak aktif.',
            ], 404);
        }

        $turbidity = (float) $request->turbidity_value;
        $rainValue = (int)   $request->rain_value;

        // Ambil threshold dari DB
        $thKeruh       = SensorThreshold::get('turbidity_keruh', 50);
        $thSangatKeruh = SensorThreshold::get('turbidity_sangat_keruh', 100);
        $thRain        = SensorThreshold::get('rain_threshold', 500);

        // Tentukan status kekeruhan
        $turbidityStatus = 'jernih';
        if ($turbidity >= $thSangatKeruh) {
            $turbidityStatus = 'sangat_keruh';
        } elseif ($turbidity >= $thKeruh) {
            $turbidityStatus = 'keruh';
        }

        $rainDetected = $rainValue < $thRain;

        // Update last_seen_at device
        $device->update(['last_seen_at' => now()]);

        // Simpan pembacaan sensor
        $reading = SensorReading::create([
            'device_id'        => $device->id,
            'turbidity_value'  => $turbidity,
            'turbidity_status' => $turbidityStatus,
            'rain_detected'    => $rainDetected,
            'rain_value'       => $rainValue,
            'esp32_online'     => true,
        ]);

        // Log hujan: catat jika terdeteksi dan interval >= 5 menit
        $rainAction = false;
        if ($rainDetected) {
            $lastRain = RainLog::where('device_id', $device->id)->latest()->first();

            if (!$lastRain || $lastRain->created_at->diffInMinutes(now()) >= 5) {
                RainLog::create([
                    'device_id'    => $device->id,
                    'rain_value'   => $rainValue,
                    'cover_closed' => true,
                    'notes'        => 'Penutup otomatis menutup karena hujan terdeteksi.',
                ]);
                $rainAction = true;
            }
        }

        // Log kaporit: catat jika keruh dan interval >= 30 menit
        $chlorineAction = false;
        if (in_array($turbidityStatus, ['keruh', 'sangat_keruh'])) {
            $lastChlor = ChlorineLog::where('device_id', $device->id)->latest()->first();

            if (!$lastChlor || $lastChlor->created_at->diffInMinutes(now()) >= 30) {
                $amount = SensorThreshold::get('chlorine_amount_ml', 50);
                if ($turbidityStatus === 'sangat_keruh') {
                    $amount *= 1.5;
                }

                ChlorineLog::create([
                    'device_id'          => $device->id,
                    'turbidity_value'    => $turbidity,
                    'turbidity_status'   => $turbidityStatus,
                    'chlorine_added'     => true,
                    'chlorine_amount_ml' => $amount,
                    'notes'              => "Kaporit {$amount}ml ditambahkan otomatis.",
                ]);
                $chlorineAction = true;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data diterima',
            'data'    => [
                'reading_id'       => $reading->id,
                'turbidity_status' => $turbidityStatus,
                'rain_detected'    => $rainDetected,
                'actions'          => [
                    'cover_closed'   => $rainAction,
                    'chlorine_added' => $chlorineAction,
                ],
            ],
        ]);
    }

    /**
     * GET /api/sensor/latest
     */
    public function latest(Request $request)
    {
        $device = Device::where('device_id', $request->device_id)
            ->where('is_active', true)
            ->first();

        if (!$device) {
            return response()->json(['success' => false, 'message' => 'Device tidak ditemukan'], 404);
        }

        $reading = SensorReading::latestByDevice($device->id);

        if (!$reading) {
            return response()->json(['success' => false, 'message' => 'Belum ada data'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $reading,
        ]);
    }

    /**
     * GET /api/sensor/thresholds
     */
    // Ganti method thresholds() yang lama dengan ini

    public function thresholds(Request $request)
    {
        $device = Device::where('device_id', $request->device_id)
            ->where('is_active', true)
            ->first();

        $deviceId = $device?->id;

        $keys = ['turbidity_keruh', 'turbidity_sangat_keruh', 'rain_threshold', 'chlorine_amount_ml'];
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = SensorThreshold::get($key, 0, $deviceId);
        }

        return response()->json(['success' => true, 'data' => $result]);
    }
    /**
     * GET /api/sensor/status
     */
    public function status(Request $request)
    {
        $device = Device::where('device_id', $request->device_id)->first();

        return response()->json([
            'success'     => true,
            'online'      => true,
            'device_id'   => $request->device_id,
            'device_name' => $device?->name,
            'server_time' => now()->toDateTimeString(),
        ]);
    }
}
