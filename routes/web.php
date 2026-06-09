<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\Admin\AdminController;

// ── Auth ───────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    if (auth()->check()) {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return redirect()->route(
            $user->role === 'admin' ? 'admin.dashboard' : 'user.dashboard'
        );
    }
    return redirect()->route('login');
});

// ── User Routes ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard',            [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/poll',       [DashboardController::class, 'pollData'])->name('dashboard.poll');
    Route::get('/rain-logs',            [DashboardController::class, 'rainLogs'])->name('rain-logs');
    Route::get('/chlorine-logs',        [DashboardController::class, 'chlorineLogs'])->name('chlorine-logs');
    Route::get('/rain-logs/export',     [DashboardController::class, 'exportRainCsv'])->name('rain-logs.export');
    Route::get('/chlorine-logs/export', [DashboardController::class, 'exportChlorineCsv'])->name('chlorine-logs.export');
    Route::get('/profile',              [DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile',              [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password',             [DashboardController::class, 'updatePassword'])->name('password.update');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', fn() => redirect()->route('login'));

// ── User Routes ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard',       [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/rain-logs',       [DashboardController::class, 'rainLogs'])->name('rain-logs');
    Route::get('/chlorine-logs',   [DashboardController::class, 'chlorineLogs'])->name('chlorine-logs');
    Route::get('/profile',         [DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile',         [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password',        [DashboardController::class, 'updatePassword'])->name('password.update');
});

// ── Admin Routes ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',            [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/poll',       [AdminController::class, 'pollDashboard'])->name('dashboard.poll');
    Route::get('/rain-logs',            [AdminController::class, 'rainLogs'])->name('rain-logs');
    Route::get('/chlorine-logs',        [AdminController::class, 'chlorineLogs'])->name('chlorine-logs');
    Route::get('/rain-logs/export',     [AdminController::class, 'exportRainCsv'])->name('rain-logs.export');
    Route::get('/chlorine-logs/export', [AdminController::class, 'exportChlorineCsv'])->name('chlorine-logs.export');

    // Kelola user
    Route::get('/users',             [AdminController::class, 'users'])->name('users');
    Route::get('/users/create',      [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users',            [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}',      [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}',   [AdminController::class, 'destroyUser'])->name('users.destroy');

    // Konfigurasi sensor
    Route::get('/sensor-config',  [AdminController::class, 'sensorConfig'])->name('sensor-config');
    Route::put('/sensor-config',  [AdminController::class, 'updateSensorConfig'])->name('sensor-config.update');

    // Kelola device
    Route::get('/devices',               [AdminController::class, 'devices'])->name('devices');
    Route::get('/devices/create',        [AdminController::class, 'createDevice'])->name('devices.create');
    Route::post('/devices',              [AdminController::class, 'storeDevice'])->name('devices.store');
    Route::get('/devices/{device}/edit', [AdminController::class, 'editDevice'])->name('devices.edit');
    Route::put('/devices/{device}',      [AdminController::class, 'updateDevice'])->name('devices.update');
    Route::delete('/devices/{device}',   [AdminController::class, 'destroyDevice'])->name('devices.destroy');
    Route::post('/devices/{device}/generate-key', [AdminController::class, 'generateApiKey'])->name('devices.generate-key');
    Route::delete('/devices/{device}/delete-key', [AdminController::class, 'deleteApiKey'])->name('devices.delete-key');
});
    Route::get('/dashboard',        [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/rain-logs',        [AdminController::class, 'rainLogs'])->name('rain-logs');
    Route::get('/chlorine-logs',    [AdminController::class, 'chlorineLogs'])->name('chlorine-logs');

    // Kelola user
    Route::get('/users',            [AdminController::class, 'users'])->name('users');
    Route::get('/users/create',     [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users',           [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit',[AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}',     [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}',  [AdminController::class, 'destroyUser'])->name('users.destroy');

    // Konfigurasi sensor
    Route::get('/sensor-config',    [AdminController::class, 'sensorConfig'])->name('sensor-config');
    Route::put('/sensor-config',    [AdminController::class, 'updateSensorConfig'])->name('sensor-config.update');
});
