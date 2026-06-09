<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');                          // ID perangkat ESP32
            $table->float('turbidity_value');                     // NTU — nilai kekeruhan
            $table->enum('turbidity_status', ['jernih', 'keruh', 'sangat_keruh']);
            $table->boolean('rain_detected')->default(false);
            $table->integer('rain_value');                        // nilai ADC mentah sensor hujan
            $table->boolean('esp32_online')->default(true);
            $table->timestamps();
        });

        Schema::create('rain_logs', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->integer('rain_value');
            $table->boolean('cover_closed')->default(true);       // apakah penutup menutup otomatis
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('chlorine_logs', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->float('turbidity_value');
            $table->enum('turbidity_status', ['keruh', 'sangat_keruh']);
            $table->boolean('chlorine_added')->default(true);     // apakah kaporit berhasil ditambahkan
            $table->float('chlorine_amount_ml')->nullable();      // estimasi ml kaporit
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sensor_thresholds', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();                      // e.g. turbidity_keruh, turbidity_sangat_keruh, rain_threshold
            $table->float('value');
            $table->string('unit')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->string('api_key', 64)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_keys');
        Schema::dropIfExists('sensor_thresholds');
        Schema::dropIfExists('chlorine_logs');
        Schema::dropIfExists('rain_logs');
        Schema::dropIfExists('sensor_readings');
    }
};
