<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Device;
use App\Models\SensorReading;
use App\Models\RainLog;
use App\Models\ChlorineLog;
use App\Models\SensorThreshold;

class AdminController extends Controller
{
    // ── Dashboard ──────────────────────────────────────────────────────────
    public function dashboard()
    {
        $userCount   = User::where('role', 'user')->count();
        $deviceCount = Device::count();
        $stats = [
            'rain_today'     => RainLog::whereDate('created_at', today())->count(),
            'chlorine_today' => ChlorineLog::whereDate('created_at', today())->count(),
            'total_users'    => $userCount,
            'total_devices'  => $deviceCount,
            'avg_turbidity'  => SensorReading::where('created_at', '>=', now()->subDay())->avg('turbidity_value'),
        ];

        $latest         = SensorReading::with('device')->latest()->first();
        $recentReadings = SensorReading::with('device')->latest()->take(10)->get();
        $devices        = Device::with('user')->latest()->get();

        return view('admin.dashboard', compact('latest', 'stats', 'recentReadings', 'devices'));
    }

    // ── Poll Dashboard ─────────────────────────────────────────────────────
    public function pollDashboard()
    {
        $stats = [
            'rain_today'     => RainLog::whereDate('created_at', today())->count(),
            'chlorine_today' => ChlorineLog::whereDate('created_at', today())->count(),
            'avg_turbidity'  => SensorReading::where('created_at', '>=', now()->subDay())
                ->avg('turbidity_value'),
        ];

        $latest         = SensorReading::with('device')->latest()->first();
        $recentReadings = SensorReading::with('device')->latest()->take(10)->get();

        return response()->json([
            'success' => true,
            'stats'   => [
                'rain_today'     => $stats['rain_today'],
                'chlorine_today' => $stats['chlorine_today'],
                'avg_turbidity'  => $stats['avg_turbidity']
                    ? number_format($stats['avg_turbidity'], 1)
                    : null,
            ],
            'latest' => $latest ? [
                'turbidity_value'  => number_format($latest->turbidity_value, 1),
                'turbidity_label'  => $latest->turbidity_label,
                'turbidity_color'  => $latest->turbidity_color,
                'rain_value'       => $latest->rain_value,
                'rain_detected'    => $latest->rain_detected,
                'updated_at'       => $latest->created_at->diffForHumans(),
                'updated_time'     => $latest->created_at->format('H:i:s'),
            ] : null,
            'recent_readings' => $recentReadings->map(fn($r) => [
                'time'            => $r->created_at->format('d/m H:i:s'),
                'device_id'       => $r->device->device_id ?? '—',
                'turbidity_value' => number_format($r->turbidity_value, 1),
                'turbidity_label' => $r->turbidity_label,
                'turbidity_color' => $r->turbidity_color,
                'rain_value'      => $r->rain_value,
                'rain_detected'   => $r->rain_detected,
            ]),
        ]);
    }

    // ── Rain Logs ──────────────────────────────────────────────────────────
    public function rainLogs(Request $request)
    {
        $logs = RainLog::with('device.user')
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->when($request->device_id, fn($q) => $q->where('device_id', $request->device_id))
            ->latest()->paginate(15);

        $devices = Device::all();

        return view('admin.rain-logs', compact('logs', 'devices'));
    }

    // ── Chlorine Logs ──────────────────────────────────────────────────────
    public function chlorineLogs(Request $request)
    {
        $logs = ChlorineLog::with('device.user')
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->when($request->device_id, fn($q) => $q->where('device_id', $request->device_id))
            ->latest()->paginate(15);

        $devices = Device::all();

        return view('admin.chlorine-logs', compact('logs', 'devices'));
    }

    // ── Kelola Device ──────────────────────────────────────────────────────
    public function devices()
    {
        $devices = Device::with('user')->oldest()->paginate(10);
        return view('admin.devices.index', compact('devices'));
    }

    public function createDevice()
    {
        $users = User::where('role', 'user')->get();
        return view('admin.devices.create', compact('users'));
    }

    public function storeDevice(Request $request)
    {
        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'device_id' => 'required|string|unique:devices,device_id',
            'name'      => 'required|string|max:100',
            'location'  => 'nullable|string|max:100',
        ]);

        Device::create($request->only('user_id', 'device_id', 'name', 'location'));

        return redirect()->route('admin.devices')->with('success', 'Device berhasil ditambahkan.');
    }

    public function editDevice(Device $device)
    {
        $users = User::where('role', 'user')->get();
        return view('admin.devices.edit', compact('device', 'users'));
    }

    public function updateDevice(Request $request, Device $device)
    {
        $request->validate([
            'user_id'   => 'required|exists:users,id',
            'device_id' => 'required|string|unique:devices,device_id,' . $device->id,
            'name'      => 'required|string|max:100',
            'location'  => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $device->update($request->only('user_id', 'device_id', 'name', 'location', 'is_active'));

        return redirect()->route('admin.devices')->with('success', 'Device berhasil diperbarui.');
    }

    public function destroyDevice(Device $device)
    {
        $device->delete();
        return redirect()->route('admin.devices')->with('success', 'Device berhasil dihapus.');
    }

    // ── Kelola User ────────────────────────────────────────────────────────
    public function users()
    {
        $users = User::where('role', 'user')->withCount('devices')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'phone'    => 'nullable|string|max:20',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user',
            'phone'    => $request->phone,
        ]);

        return redirect()->route('admin.users')->with('success', 'Akun pengguna berhasil dibuat.');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($request->only('name', 'email', 'phone'));

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroyUser(User $user)
    {
        if ($user->isAdmin()) {
            return back()->withErrors(['error' => 'Tidak bisa hapus akun admin.']);
        }
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'Akun berhasil dihapus.');
    }

    // ── Konfigurasi Sensor ─────────────────────────────────────────────────
    public function sensorConfig()
    {
        $thresholds = SensorThreshold::all()->keyBy('key');
        return view('admin.sensor-config', compact('thresholds'));
    }

    public function updateSensorConfig(Request $request)
    {
        $request->validate([
            'turbidity_keruh'        => 'required|numeric|min:0',
            'turbidity_sangat_keruh' => 'required|numeric|min:0',
            'rain_threshold'         => 'required|numeric|min:0',
            'chlorine_amount_ml'     => 'required|numeric|min:0',
        ]);

        foreach ($request->only('turbidity_keruh', 'turbidity_sangat_keruh', 'rain_threshold', 'chlorine_amount_ml') as $key => $value) {
            SensorThreshold::where('key', $key)->update(['value' => $value]);
        }

        return back()->with('success', 'Konfigurasi sensor berhasil disimpan.');
    }

    // ── Export CSV ─────────────────────────────────────────────────────────
    public function exportRainCsv(Request $request)
    {
        $logs = RainLog::with('device')
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->when($request->device_id, fn($q) => $q->where('device_id', $request->device_id))
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

    public function exportChlorineCsv(Request $request)
    {
        $logs = ChlorineLog::with('device')
            ->when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->when($request->device_id, fn($q) => $q->where('device_id', $request->device_id))
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
    }

    // ── API Key ────────────────────────────────────────────────────────────
    public function generateApiKey(Device $device)
    {
        $device->apiKey()->delete();

        \App\Models\ApiKey::create([
            'device_id' => $device->id,
            'api_key'   => bin2hex(random_bytes(32)),
            'is_active' => true,
        ]);

        return back()->with('success', 'API key berhasil digenerate.');
    }

    public function deleteApiKey(Device $device)
    {
        $device->apiKey()->delete();
        return back()->with('success', 'API key berhasil dihapus.');
    }
}