<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMataPelajaranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->integer('mata_pelajaran_id');
			$table->string('nama');
			$table->decimal('pilihan_sekolah', 1, 0);
			$table->decimal('pilihan_buku', 1, 0);
			$table->decimal('pilihan_kepengawasan', 1, 0);
            $table->decimal('pilihan_evaluasi', 1, 0);
			$table->string('jurusan_id', 25)->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->foreign('jurusan_id')->references('jurusan_id')->on('jurusan')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->primary('mata_pelajaran_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mata_pelajaran', function (Blueprint $table) {
            $table->dropForeign(['jurusan_id']);
        });
        Schema::dropIfExists('mata_pelajaran');
    }
}
