<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SensorThreshold;

class SensorThresholdSeeder extends Seeder
{
    public function run(): void
    {
        $thresholds = [
            [
                'key'         => 'turbidity_keruh',
                'value'       => 50,
                'unit'        => 'NTU',
                'description' => 'Batas kekeruhan air kategori keruh',
            ],
            [
                'key'         => 'turbidity_sangat_keruh',
                'value'       => 100,
                'unit'        => 'NTU',
                'description' => 'Batas kekeruhan air kategori sangat keruh',
            ],
            [
                'key'         => 'rain_threshold',
                'value'       => 500,
                'unit'        => 'ADC',
                'description' => 'Batas nilai ADC sensor hujan',
            ],
            [
                'key'         => 'chlorine_amount_ml',
                'value'       => 50,
                'unit'        => 'ml',
                'description' => 'Jumlah kaporit yang ditambahkan otomatis',
            ],
        ];

        foreach ($thresholds as $threshold) {
            SensorThreshold::updateOrCreate(
                ['key' => $threshold['key']],
                $threshold
            );
        }
    }
}