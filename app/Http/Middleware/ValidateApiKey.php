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

        $apiKey = ApiKey::where('api_key', $key)->where('is_active', true)->first();

        if (!$apiKey) {
            return response()->json(['success' => false, 'message' => 'API key tidak valid'], 403);
        }

        $apiKey->update(['last_used_at' => now()]);
        $request->merge(['device_id' => $apiKey->device_id]);

        return $next($request);
    }
}
