<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUjiansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ujians', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('event_id');
            $table->integer('mata_pelajaran_id')->unsigned();
            $table->date('tanggal');
            $table->foreign('event_id')->references('id')->on('events')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ujians', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
        });
        Schema::dropIfExists('ujians');
    }
}
