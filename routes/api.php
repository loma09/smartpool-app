<?php
// routes/api.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SensorController;

/*
|--------------------------------------------------------------------------
| API Routes — digunakan oleh ESP32
| Header: X-API-Key: {api_key}
|--------------------------------------------------------------------------
*/

Route::middleware('api.key')->prefix('sensor')->group(function () {
    Route::post('/data',       [SensorController::class, 'store']);      // POST kirim data sensor
    Route::get('/latest',      [SensorController::class, 'latest']);     // GET data terakhir
    Route::get('/thresholds',  [SensorController::class, 'thresholds']); // GET konfigurasi threshold
    Route::get('/status',      [SensorController::class, 'status']);     // GET heartbeat
});
