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
        Schema::create('ai_xray', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name')->nullable();
            $table->string('path')->nullable();
            $table->float('measurement_mm')->default(0);
            $table->string('edited_by')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_xray');
    }
};
