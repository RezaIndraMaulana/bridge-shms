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
        Schema::create('bridges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained('cities')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('rating')->nullable();
            $table->string('photo_url')->nullable();
            $table->string('maintenance_status')->nullable(); // e.g. 'Preventive Maintenance'
            $table->string('support_type')->nullable();
            $table->decimal('box_beam_length', 8, 2)->nullable();
            $table->decimal('box_beam_inside_width', 8, 2)->nullable();
            $table->decimal('box_beam_outside_width', 8, 2)->nullable();
            $table->decimal('box_beam_inside_height', 8, 2)->nullable();
            $table->decimal('box_beam_outside_height', 8, 2)->nullable();
            $table->integer('number_of_box_beams')->nullable();
            $table->integer('number_of_diaphragms_per_box_beam')->nullable();
            $table->decimal('thickness_at_diaphragms', 8, 2)->nullable();
            $table->decimal('thickness_of_box_beam_end', 8, 2)->nullable();
            $table->decimal('thickness_of_wearing_surface', 8, 2)->nullable();
            $table->decimal('compressive_strength_28_days', 8, 2)->nullable();
            $table->decimal('unit_weight_of_concrete', 8, 2)->nullable();
            $table->foreignId('company_id')->nullable()->after('city_id')->constrained('companies')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bridges');
    }
};
