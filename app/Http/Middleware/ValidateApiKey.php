<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiKey;

class ValidateApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('X-API-Key') ?? $request->query('api_key');

        if (!$key) {
            return response()->json(['success' => false, 'message' => 'API key diperlukan'], 401);
        }

        $apiKey = ApiKey::where('api_key', $key)
            ->where('is_active', true)
            ->with('device') // eager load supaya tidak N+1
            ->first();

        if (!$apiKey) {
            return response()->json(['success' => false, 'message' => 'API key tidak valid'], 403);
        }

        // Cek device masih ada dan aktif
        if (!$apiKey->device || !$apiKey->device->is_active) {
            return response()->json(['success' => false, 'message' => 'Device tidak aktif'], 403);
        }

        $apiKey->update(['last_used_at' => now()]);
        $request->merge(['device_id' => $apiKey->device->device_id]);

        return $next($request);
    }
}