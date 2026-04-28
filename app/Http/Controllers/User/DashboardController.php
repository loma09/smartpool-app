<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\SensorReading;
use App\Models\RainLog;
use App\Models\ChlorineLog;
use App\Models\ApiKey;

class DashboardController extends Controller
{
    private string $deviceId = 'ESP32-001';
    public function index()
    {
        $latest      = SensorReading::latestByDevice($this->deviceId);
        $rainLogs    = RainLog::where('device_id', $this->deviceId)->latest()->take(5)->get();
        $chlorLogs   = ChlorineLog::where('device_id', $this->deviceId)->latest()->take(5)->get();
        $esp32Online = $this->checkEsp32Status();

        // Statistik 24 jam terakhir
        $stats = [
            'rain_count'     => RainLog::where('device_id', $this->deviceId)
                ->where('created_at', '>=', now()->subDay())->count(),
            'chlorine_count' => ChlorineLog::where('device_id', $this->deviceId)
                ->where('created_at', '>=', now()->subDay())->count(),
            'avg_turbidity'  => SensorReading::where('device_id', $this->deviceId)
                ->where('created_at', '>=', now()->subDay())
                ->avg('turbidity_value'),
        ];

        return view('user.dashboard', compact('latest', 'rainLogs', 'chlorLogs', 'esp32Online', 'stats'));
    }

    public function rainLogs(Request $request)
    {
        $logs = RainLog::where('device_id', $this->deviceId)
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->latest()
            ->paginate(15);

        return view('user.rain-logs', compact('logs'));
    }

    public function chlorineLogs(Request $request)
    {
        $logs = ChlorineLog::where('device_id', $this->deviceId)
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->latest()
            ->paginate(15);

        return view('user.chlorine-logs', compact('logs'));
    }

    public function profile()
    {
        return view('user.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'  => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($request->only('name', 'phone'));

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    private function checkEsp32Status(): bool
    {
        $latest = SensorReading::where('device_id', $this->deviceId)->latest()->first();
        if (!$latest) return false;
        // Anggap offline jika tidak ada data dalam 2 menit
        return $latest->created_at->diffInMinutes(now()) < 2;
    }
}
