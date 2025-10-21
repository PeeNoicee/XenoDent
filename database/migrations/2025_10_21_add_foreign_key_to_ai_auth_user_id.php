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
        Schema::table('ai_auth', function (Blueprint $table) {
            // First, change the data type from integer to unsignedBigInteger to match users.id
            $table->unsignedBigInteger('user_id')->nullable()->change();
            
            // Add foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_auth', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['user_id']);
            
            // Change back to integer (optional, but for completeness)
            $table->integer('user_id')->nullable()->change();
        });
    }
};
