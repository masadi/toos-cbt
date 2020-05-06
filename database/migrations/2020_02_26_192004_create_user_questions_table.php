<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_questions', function (Blueprint $table) {
            $table->uuid('user_question_id');
            $table->uuid('user_exam_id');
            $table->uuid('question_id');
            $table->uuid('anggota_rombel_id')->nullable();
            $table->uuid('ptk_id')->nullable();
            $table->uuid('answer_id')->nullable();
            $table->decimal('ragu', 1, 0)->nullable();
            $table->timestamps();
            $table->foreign('user_exam_id')->references('user_exam_id')->on('user_exams')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('question_id')->references('question_id')->on('questions')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('anggota_rombel_id')->references('anggota_rombel_id')->on('anggota_rombel')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('ptk_id')->references('ptk_id')->on('ptk')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('answer_id')->references('answer_id')->on('answers')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->primary('user_question_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_questions', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
            $table->dropForeign(['anggota_rombel_id']);
            $table->dropForeign(['ptk_id']);
            $table->dropForeign(['answer_id']);
            $table->dropForeign(['user_exam_id']);
        });
        Schema::dropIfExists('user_questions');
    }
}
