<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\Admin\AdminController;

// ── Auth ───────────────────────────────────────────────────────────────────
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
