<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('dispositions', function (Blueprint $table) {
            // Make existing to_user_id nullable
            $table->unsignedBigInteger('to_user_id')->nullable()->change();

            $table->foreignId('to_unit_id')
                ->nullable()
                ->after('to_user_id')
                ->constrained('units')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('dispositions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('to_unit_id');
            $table->unsignedBigInteger('to_user_id')->nullable(false)->change();
        });
    }
};
