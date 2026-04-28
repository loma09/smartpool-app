<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\SensorThreshold;
use App\Models\ApiKey;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin default
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@smartpool.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // User demo
        User::create([
            'name'     => 'Pemilik Kolam',
            'email'    => 'user@smartpool.com',
            'password' => Hash::make('user123'),
            'role'     => 'user',
        ]);

        // Threshold default sensor
        $thresholds = [
            ['key' => 'turbidity_keruh',       'value' => 50,   'unit' => 'NTU',  'description' => 'Batas bawah air keruh'],
            ['key' => 'turbidity_sangat_keruh', 'value' => 100,  'unit' => 'NTU',  'description' => 'Batas sangat keruh'],
            ['key' => 'rain_threshold',         'value' => 500,  'unit' => 'ADC',  'description' => 'Nilai ADC pendeteksi hujan (di bawah = hujan)'],
            ['key' => 'chlorine_amount_ml',     'value' => 50,   'unit' => 'ml',   'description' => 'Jumlah kaporit yang ditambahkan saat keruh'],
        ];

        foreach ($thresholds as $t) {
            SensorThreshold::create($t);
        }

        // API key untuk ESP32
        ApiKey::create([
            'device_id' => 'ESP32-POOL-001',
            'api_key'   => Str::random(32),
            'is_active' => true,
        ]);
    }
}
