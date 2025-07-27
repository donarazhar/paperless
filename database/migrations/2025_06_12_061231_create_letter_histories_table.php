<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLetterHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('letter_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('letter_id')
                ->constrained('letters')
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->string('action');   // e.g. 'sent', 'draft', 'read', 'disposed'
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('letter_histories');
    }
}
