<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sensor_thresholds', function (Blueprint $table) {
            // Hapus unique constraint lama
            $table->dropUnique('sensor_thresholds_key_unique');

            $table->foreignId('device_id')
                ->nullable()
                ->after('id')
                ->constrained('devices')
                ->onDelete('cascade');

            // Buat unique constraint baru: kombinasi key + device_id
            $table->unique(['key', 'device_id']);
        });
    }

    public function down(): void
    {
        Schema::table('sensor_thresholds', function (Blueprint $table) {
            $table->dropUnique(['key', 'device_id']);
            $table->dropForeign(['device_id']);
            $table->dropColumn('device_id');
            $table->unique('key');
        });
    }
};
