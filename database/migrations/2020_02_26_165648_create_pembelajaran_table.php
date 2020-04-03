<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembelajaranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pembelajaran', function (Blueprint $table) {
            $table->uuid('pembelajaran_id');
			$table->uuid('sekolah_id');
			$table->string('semester_id', 5);
			$table->uuid('rombongan_belajar_id');
			$table->uuid('ptk_id')->nullable();
			$table->integer('mata_pelajaran_id');
			$table->string('nama_mata_pelajaran');
			$table->timestamps();
			$table->softDeletes();
			$table->foreign('sekolah_id')->references('sekolah_id')->on('sekolah')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('mata_pelajaran_id')->references('mata_pelajaran_id')->on('mata_pelajaran')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('rombongan_belajar_id')->references('rombongan_belajar_id')->on('rombongan_belajar')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('semester_id')->references('semester_id')->on('semester')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('ptk_id')->references('ptk_id')->on('ptk')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->primary('pembelajaran_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pembelajaran', function (Blueprint $table) {
            $table->dropForeign(['sekolah_id']);
			$table->dropForeign(['mata_pelajaran_id']);
			$table->dropForeign(['rombongan_belajar_id']);
			$table->dropForeign(['semester_id']);
			$table->dropForeign(['ptk_id']);
        });
        Schema::dropIfExists('pembelajaran');
    }
}
