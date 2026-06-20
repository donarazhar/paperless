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
        Schema::table('letters', function (Blueprint $table) {
            $table->foreignId('from_user_id')->nullable()->change();
            $table->string('external_sender_name')->nullable()->after('from_user_id');
            $table->foreignId('created_by_user_id')->nullable()->after('external_sender_name')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropForeign(['created_by_user_id']);
            $table->dropColumn(['external_sender_name', 'created_by_user_id']);
            // Revert from_user_id back to non-nullable (might fail if there are nulls)
            $table->foreignId('from_user_id')->nullable(false)->change();
        });
    }
};
