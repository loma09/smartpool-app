<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SensorReading;
use App\Models\RainLog;
use App\Models\ChlorineLog;
use App\Models\SensorThreshold;

class SensorController extends Controller
{
    /**
     * POST /api/sensor/data
     * Menerima data dari ESP32 dan menyimpan ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'turbidity_value' => 'required|numeric',
            'rain_value'      => 'required|integer',
        ]);

        $deviceId  = $request->device_id;
        $turbidity = (float) $request->turbidity_value;
        $rainValue = (int)   $request->rain_value;

        // Ambil threshold dari DB
        $thKeruh      = SensorThreshold::get('turbidity_keruh', 50);
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

        // Simpan pembacaan sensor
        $reading = SensorReading::create([
            'device_id'        => $deviceId,
            'turbidity_value'  => $turbidity,
            'turbidity_status' => $turbidityStatus,
            'rain_detected'    => $rainDetected,
            'rain_value'       => $rainValue,
            'esp32_online'     => true,
        ]);

        // Log hujan jika terdeteksi
        $rainAction = false;
        if ($rainDetected) {
            // Cek apakah log hujan terakhir sudah lebih dari 5 menit
            $lastRain = RainLog::where('device_id', $deviceId)
                ->latest()->first();

            if (!$lastRain || $lastRain->created_at->diffInMinutes(now()) >= 5) {
                RainLog::create([
                    'device_id'   => $deviceId,
                    'rain_value'  => $rainValue,
                    'cover_closed' => true,
                    'notes'       => 'Penutup otomatis menutup karena hujan terdeteksi.',
                ]);
                $rainAction = true;
            }
        }

        // Log kaporit jika keruh
        $chlorineAction = false;
        if (in_array($turbidityStatus, ['keruh', 'sangat_keruh'])) {
            $lastChlor = ChlorineLog::where('device_id', $deviceId)
                ->latest()->first();

            if (!$lastChlor || $lastChlor->created_at->diffInMinutes(now()) >= 30) {
                $amount = SensorThreshold::get('chlorine_amount_ml', 50);
                if ($turbidityStatus === 'sangat_keruh') {
                    $amount *= 1.5;
                }

                ChlorineLog::create([
                    'device_id'         => $deviceId,
                    'turbidity_value'   => $turbidity,
                    'turbidity_status'  => $turbidityStatus,
                    'chlorine_added'    => true,
                    'chlorine_amount_ml' => $amount,
                    'notes'             => "Kaporit {$amount}ml ditambahkan otomatis.",
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
                    'cover_closed'    => $rainAction,
                    'chlorine_added'  => $chlorineAction,
                ],
            ],
        ]);
    }

    /**
     * GET /api/sensor/latest
     * Mengembalikan data sensor terbaru untuk ESP32.
     */
    public function latest(Request $request)
    {
        $deviceId = $request->device_id;
        $reading  = SensorReading::latestByDevice($deviceId);

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
     * Mengembalikan konfigurasi threshold untuk ESP32.
     */
    public function thresholds()
    {
        $thresholds = \App\Models\SensorThreshold::all()->keyBy('key')
            ->map(fn($t) => $t->value);

        return response()->json(['success' => true, 'data' => $thresholds]);
    }

    /**
     * GET /api/sensor/status
     * Endpoint heartbeat ESP32.
     */
    public function status(Request $request)
    {
        return response()->json([
            'success'   => true,
            'online'    => true,
            'device_id' => $request->device_id,
            'server_time' => now()->toDateTimeString(),
        ]);
    }
}
