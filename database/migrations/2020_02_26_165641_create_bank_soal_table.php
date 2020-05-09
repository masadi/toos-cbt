<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankSoalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_soal', function (Blueprint $table) {
            $table->uuid('bank_soal_id');
            $table->integer('tingkat_pendidikan_id')->unsigned();
            $table->integer('mata_pelajaran_id');
            $table->uuid('ptk_id');
            $table->integer('soal_ke')->unsigned();
            $table->text('soal');
            $table->timestamps();
            $table->foreign('mata_pelajaran_id')->references('mata_pelajaran_id')->on('mata_pelajaran')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('ptk_id')->references('ptk_id')->on('ptk')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->primary('bank_soal_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_soal', function (Blueprint $table) {
            $table->dropForeign(['mata_pelajaran_id']);
            $table->dropForeign(['ptk_id']);
        });
        Schema::dropIfExists('bank_soal');
    }
}
