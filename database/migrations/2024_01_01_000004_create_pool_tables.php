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
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->float('turbidity_value');
            $table->enum('turbidity_status', ['jernih', 'keruh', 'sangat_keruh']);
            $table->boolean('rain_detected')->default(false);
            $table->integer('rain_value');
            $table->boolean('esp32_online')->default(true);
            $table->timestamps();
        });

        Schema::create('rain_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->integer('rain_value');
            $table->boolean('cover_closed')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('chlorine_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->float('turbidity_value');
            $table->enum('turbidity_status', ['keruh', 'sangat_keruh']);
            $table->boolean('chlorine_added')->default(true);
            $table->float('chlorine_amount_ml')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sensor_thresholds', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->float('value');
            $table->string('unit')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
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