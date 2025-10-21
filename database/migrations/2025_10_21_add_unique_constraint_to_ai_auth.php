<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ai_auth', function (Blueprint $table) {
            // First, remove any duplicate records (keep the latest one for each user_id)
            DB::statement('
                DELETE a1 FROM ai_auth a1
                INNER JOIN ai_auth a2 
                WHERE a1.id < a2.id 
                AND a1.user_id = a2.user_id
            ');
            
            // Add unique constraint to user_id
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_auth', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
        });
    }
};
