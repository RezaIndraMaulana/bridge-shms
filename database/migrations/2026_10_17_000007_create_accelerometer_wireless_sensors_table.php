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
        Schema::create('accelerometer_wireless_sensors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bridge_id')->constrained('bridges')->cascadeOnDelete();
            $table->string('brand_model')->nullable();
            $table->string('mac_address')->unique();
            $table->string('name')->nullable(); // e.g. 'Sunspot WSN'
            $table->string('type')->nullable(); // e.g. 'A12'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accelerometer_wireless_sensors');
    }
};
