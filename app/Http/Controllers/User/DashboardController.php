<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Device;
use App\Models\SensorReading;
use App\Models\RainLog;
use App\Models\ChlorineLog;

class DashboardController extends Controller
{
    // Ambil device milik user yang sedang login
    private function getDevice(Request $request, $deviceId = null)
    {
        $user = auth()->user();
        if ($deviceId) {
            return Device::where('user_id', $user->id)->where('id', $deviceId)->firstOrFail();
        }
        return Device::where('user_id', $user->id)->where('is_active', true)->first();
    }

    public function index(Request $request)
    {
        $user    = auth()->user();
        $devices = Device::where('user_id', $user->id)->where('is_active', true)->get();
        $device  = $this->getDevice($request, $request->device_id);

        if (!$device) {
            return view('user.dashboard', compact('devices'))->with('no_device', true);
        }

        $latest    = SensorReading::latestByDevice($device->id);
        $rainLogs  = RainLog::where('device_id', $device->id)->latest()->take(5)->get();
        $chlorLogs = ChlorineLog::where('device_id', $device->id)->latest()->take(5)->get();

        $stats = [
            'rain_count'    => RainLog::where('device_id', $device->id)
                ->where('created_at', '>=', now()->subDay())->count(),
            'chlorine_count' => ChlorineLog::where('device_id', $device->id)
                ->where('created_at', '>=', now()->subDay())->count(),
            'avg_turbidity' => SensorReading::where('device_id', $device->id)
                ->where('created_at', '>=', now()->subDay())->avg('turbidity_value'),
        ];

        $esp32Online = $device->isOnline();

        return view('user.dashboard', compact('devices', 'device', 'latest', 'rainLogs', 'chlorLogs', 'esp32Online', 'stats'));
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
        $user    = auth()->user();
        $devices = Device::where('user_id', $user->id)->where('is_active', true)->get();
        $device  = $this->getDevice($request, $request->device_id);

        if (!$device) {
            $logs = collect();
            return view('user.rain-logs', compact('devices', 'logs'));
        }

        $logs = RainLog::where('device_id', $device->id)
        $logs = RainLog::where('device_id', $this->deviceId)
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->latest()
            ->paginate(15);

        return view('user.rain-logs', compact('logs', 'devices', 'device'));
        return view('user.rain-logs', compact('logs'));
    }

    public function chlorineLogs(Request $request)
    {
        $user    = auth()->user();
        $devices = Device::where('user_id', $user->id)->where('is_active', true)->get();
        $device  = $this->getDevice($request, $request->device_id);

        if (!$device) {
            $logs = collect();
            return view('user.chlorine-logs', compact('devices', 'logs'));
        }

        $logs = ChlorineLog::where('device_id', $device->id)
        $logs = ChlorineLog::where('device_id', $this->deviceId)
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->latest()
            ->paginate(15);

        return view('user.chlorine-logs', compact('logs', 'devices', 'device'));
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

    public function exportRainCsv(Request $request)
    {
        $device = $this->getDevice($request, $request->device_id);
        $logs   = RainLog::where('device_id', $device->id)
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->latest()->get();

        $filename = 'log-hujan-' . now()->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Waktu', 'Device', 'Nilai ADC', 'Penutup Otomatis', 'Catatan']);
            foreach ($logs as $i => $log) {
                fputcsv($file, [
                    $i + 1,
                    $log->created_at->format('d/m/Y H:i:s'),
                    $log->device->device_id ?? '-',
                    $log->rain_value,
                    $log->cover_closed ? 'Ya' : 'Tidak',
                    $log->notes,
                ]);
            }
            fclose($file);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
    public function pollData(Request $request)
    {
        $device = $this->getDevice($request, $request->device_id);

        if (!$device) {
            return response()->json(['success' => false], 404);
        }

        $latest    = SensorReading::latestByDevice($device->id);
        $rainLogs  = RainLog::where('device_id', $device->id)->latest()->take(5)->get();
        $chlorLogs = ChlorineLog::where('device_id', $device->id)->latest()->take(5)->get();

        $stats = [
            'rain_count'    => RainLog::where('device_id', $device->id)
                ->where('created_at', '>=', now()->subDay())->count(),
            'chlorine_count' => ChlorineLog::where('device_id', $device->id)
                ->where('created_at', '>=', now()->subDay())->count(),
            'avg_turbidity' => SensorReading::where('device_id', $device->id)
                ->where('created_at', '>=', now()->subDay())->avg('turbidity_value'),
        ];

        return response()->json([
            'success'     => true,
            'esp32_online' => $device->isOnline(),
            'latest'      => $latest ? [
                'turbidity_value'  => number_format($latest->turbidity_value, 1),
                'turbidity_status' => $latest->turbidity_status,
                'turbidity_label'  => $latest->turbidity_label,
                'turbidity_color'  => $latest->turbidity_color,
                'rain_value'       => $latest->rain_value,
                'rain_detected'    => $latest->rain_detected,
                'updated_at'       => $latest->created_at->diffForHumans(),
                'updated_time'     => $latest->created_at->format('H:i:s'),
            ] : null,
            'stats' => $stats,
            'rain_logs' => $rainLogs->map(fn($l) => [
                'time'         => $l->created_at->format('d/m H:i'),
                'rain_value'   => $l->rain_value,
                'cover_closed' => $l->cover_closed,
            ]),
            'chlor_logs' => $chlorLogs->map(fn($l) => [
                'time'               => $l->created_at->format('d/m H:i'),
                'turbidity_value'    => number_format($l->turbidity_value, 1),
                'chlorine_amount_ml' => $l->chlorine_amount_ml,
                'chlorine_added'     => $l->chlorine_added,
            ]),
        ]);
    }

    public function exportChlorineCsv(Request $request)
    {
        $device = $this->getDevice($request, $request->device_id);
        $logs   = ChlorineLog::where('device_id', $device->id)
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->latest()->get();

        $filename = 'log-kaporit-' . now()->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['#', 'Waktu', 'Device', 'Kekeruhan', 'Status', 'Kaporit Ditambah', 'Jumlah (ml)', 'Catatan']);
            foreach ($logs as $i => $log) {
                fputcsv($file, [
                    $i + 1,
                    $log->created_at->format('d/m/Y H:i:s'),
                    $log->device->device_id ?? '-',
                    $log->turbidity_value,
                    $log->turbidity_status,
                    $log->chlorine_added ? 'Ya' : 'Tidak',
                    $log->chlorine_amount_ml,
                    $log->notes,
                ]);
            }
            fclose($file);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
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
