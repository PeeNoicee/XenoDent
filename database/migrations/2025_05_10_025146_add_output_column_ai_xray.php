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
            $table->string('output_image')->nullable()->after('edited_by')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('ai_xray', function (Blueprint $table) {
            $table->dropColumn('output_image');
        });
    }
};
