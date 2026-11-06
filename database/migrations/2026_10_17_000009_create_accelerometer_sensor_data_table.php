<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accelerometer_sensor_data', function (Blueprint $table) {
            // No auto-increment id — composite primary key is used instead
            $table->unsignedBigInteger('accelerometer_wireless_sensor_id');
            $table->timestamp('recorded_at');
            $table->float('frequency_hz');
            $table->float('amplitude');

            // Composite Primary Key
            $table->primary(['accelerometer_wireless_sensor_id', 'recorded_at']);

            // Foreign Key
            $table->foreign('accelerometer_wireless_sensor_id')
                  ->references('id')
                  ->on('accelerometer_wireless_sensors')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accelerometer_sensor_data');
    }
};
