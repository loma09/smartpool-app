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
     * Menerima data dari ESP32 dan menyimpan ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'device_id'        => 'required|string',
            'turbidity_value'  => 'required|numeric',
            'turbidity_status' => 'required|in:jernih,keruh,sangat_keruh',
            'rain_detected'    => 'required|boolean',
            'rain_value'       => 'required|integer',
            'esp32_online'     => 'required|boolean',
        ]);

        // Cari device berdasarkan device_id string
        $device = Device::where('device_id', $request->device_id)
            ->where('is_active', true)
            ->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Device tidak ditemukan atau tidak aktif.',
            ], 404);
        }

        $turbidity       = (float) $request->turbidity_value;
        $rainValue       = (int)   $request->rain_value;

        // ✅ Pakai nilai langsung dari ESP32
        $turbidityStatus = $request->turbidity_status;
        $rainDetected    = (bool) $request->rain_detected;

        // Update last_seen_at device
        $device->update(['last_seen_at' => now()]);

        // Simpan pembacaan sensor (setiap request selalu disimpan)
        $reading = SensorReading::create([
            'device_id'        => $device->id,
            'turbidity_value'  => $turbidity,
            'turbidity_status' => $turbidityStatus,
            'rain_detected'    => $rainDetected,
            'rain_value'       => $rainValue,
            'esp32_online'     => true,
        ]);

        // ✅ Log hujan: catat setiap kali status hujan BERUBAH dari tidak hujan → hujan
        // Mencegah spam log saat hujan terus-menerus, tapi tetap mencatat setiap event baru
        $rainAction = false;
        if ($rainDetected) {
            $lastRain = RainLog::where('device_id', $device->id)->latest()->first();
            $lastWasRain = $lastRain && $lastRain->created_at->diffInMinutes(now()) < 5;

            if (!$lastWasRain) {
                RainLog::create([
                    'device_id'    => $device->id,
                    'rain_value'   => $rainValue,
                    'cover_closed' => true,
                    'notes'        => 'Penutup otomatis menutup karena hujan terdeteksi.',
                ]);
                $rainAction = true;
            }
        }

        // ✅ Log kaporit: catat setiap kali status keruh BERUBAH (bukan sekadar interval waktu)
        $chlorineAction = false;
        if (in_array($turbidityStatus, ['keruh', 'sangat_keruh'])) {
            $lastChlor = ChlorineLog::where('device_id', $device->id)->latest()->first();

            // Catat jika: belum pernah ada log, atau sudah lebih dari 5 menit sejak log terakhir
            $melewatiInterval = !$lastChlor || $lastChlor->created_at->diffInMinutes(now()) >= 5;

            if ($melewatiInterval) {
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
    public function thresholds()
    {
        $thresholds = SensorThreshold::all()->keyBy('key')
            ->map(fn($t) => $t->value);

        return response()->json(['success' => true, 'data' => $thresholds]);
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