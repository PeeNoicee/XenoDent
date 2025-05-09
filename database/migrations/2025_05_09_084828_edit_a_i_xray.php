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
        Schema::table('ai_xray', function (Blueprint $table) {
            $table->dropColumn('measurement_mm');
        });
    }

    public function down(): void
    {
        Schema::table('ai_xray', function (Blueprint $table) {
            $table->float('measurement_mm')->default(0);
        });
    }
};
