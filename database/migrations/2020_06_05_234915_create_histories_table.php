<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('exam_id');
            $table->uuid('user_id');
            $table->text('questions');
            $table->timestamps();
            $table->primary('id');
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id')->references('user_id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('histories', function (Blueprint $table) {
            $table->dropForeign(['exam_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('histories');
    }
}
