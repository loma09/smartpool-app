<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\SensorReading;
use App\Models\RainLog;
use App\Models\ChlorineLog;
use App\Models\SensorThreshold;

class AdminController extends Controller
{
    private string $deviceId = 'ESP32-POOL-001';

    // ── Dashboard ──────────────────────────────────────────────────────────
    public function dashboard()
    {
        $latest    = SensorReading::latestByDevice($this->deviceId);
        $userCount = User::where('role', 'user')->count();
        $stats = [
            'rain_today'     => RainLog::whereDate('created_at', today())->count(),
            'chlorine_today' => ChlorineLog::whereDate('created_at', today())->count(),
            'total_users'    => $userCount,
            'avg_turbidity'  => SensorReading::where('created_at', '>=', now()->subDay())
                ->avg('turbidity_value'),
        ];

        $recentReadings = SensorReading::latest()->take(10)->get();

        return view('admin.dashboard', compact('latest', 'stats', 'recentReadings'));
    }

    // ── Rain Logs ──────────────────────────────────────────────────────────
    public function rainLogs(Request $request)
    {
        $logs = RainLog::when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->latest()->paginate(15);

        return view('admin.rain-logs', compact('logs'));
    }

    // ── Chlorine Logs ──────────────────────────────────────────────────────
    public function chlorineLogs(Request $request)
    {
        $logs = ChlorineLog::when($request->date, fn($q) => $q->whereDate('created_at', $request->date))
            ->latest()->paginate(15);

        return view('admin.chlorine-logs', compact('logs'));
    }

    // ── Kelola User ────────────────────────────────────────────────────────
    public function users()
    {
        $users = User::where('role', 'user')->latest()->paginate(10);
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
            'turbidity_keruh'       => 'required|numeric|min:0',
            'turbidity_sangat_keruh' => 'required|numeric|min:0',
            'rain_threshold'        => 'required|numeric|min:0',
            'chlorine_amount_ml'    => 'required|numeric|min:0',
        ]);

        foreach ($request->only('turbidity_keruh', 'turbidity_sangat_keruh', 'rain_threshold', 'chlorine_amount_ml') as $key => $value) {
            SensorThreshold::where('key', $key)->update(['value' => $value]);
        }

        return back()->with('success', 'Konfigurasi sensor berhasil disimpan.');
    }
}
