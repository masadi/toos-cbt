<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->uuid('question_id');
            $table->uuid('exam_id');
            $table->integer('soal_ke')->unsigned();
            $table->longText('question');
            $table->uuid('bank_soal_id')->nullable();
            $table->timestamps();
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('bank_soal_id')->references('bank_soal_id')->on('bank_soal')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->primary('question_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['exam_id']);
            $table->dropForeign(['bank_soal_id']);
        });
        Schema::dropIfExists('questions');
    }
}
