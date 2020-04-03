<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->uuid('exam_id');
            $table->uuid('pembelajaran_id');
            $table->integer('mata_pelajaran_id')->unsigned();
            $table->string('nama');
            //$table->date('start');
            //$table->date('end');
            $table->integer('jumlah_soal')->unsigned();
            //$table->integer('jumlah_opsi')->unsigned();            
            $table->integer('durasi')->unsigned();
            $table->integer('sinkron')->unsigned()->nullable();
            $table->string('token', 6)->nullable();
            $table->integer('aktif')->unsigned()->nullable()->default(0);
            $table->timestamps();
            $table->foreign('pembelajaran_id')->references('pembelajaran_id')->on('pembelajaran')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('mata_pelajaran_id')->references('mata_pelajaran_id')->on('mata_pelajaran')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->primary('exam_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['pembelajaran_id']);
            $table->dropForeign(['mata_pelajaran_id']);
        });
        Schema::dropIfExists('exams');
    }
}
