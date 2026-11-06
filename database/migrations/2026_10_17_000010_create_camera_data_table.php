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
        Schema::create('camera_data', function (Blueprint $table) {
            // No auto-increment id — composite primary key is used instead
            $table->unsignedBigInteger('camera_id');
            $table->timestamp('recorded_at');
            $table->boolean('truck_detected');
            $table->string('image_url')->nullable();

            // Composite Primary Key
            $table->primary(['camera_id', 'recorded_at']);

            // Foreign Key
            $table->foreign('camera_id')
                  ->references('id')
                  ->on('cameras')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camera_data');
    }
};
