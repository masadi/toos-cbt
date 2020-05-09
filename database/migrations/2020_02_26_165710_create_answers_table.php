<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->uuid('answer_id');
            $table->uuid('question_id');
            $table->longText('answer')->nullable();
            $table->decimal('jawaban_ke',1,0);
            $table->decimal('correct',1,0);
            $table->timestamps();
            $table->foreign('question_id')->references('question_id')->on('questions')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->primary('answer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
        });
        Schema::dropIfExists('answers');
    }
}
