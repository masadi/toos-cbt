<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJurusanSpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jurusan_sp', function (Blueprint $table) {
            $table->uuid('jurusan_sp_id');
			$table->uuid('sekolah_id');
			$table->string('jurusan_id');
			$table->string('nama_jurusan_sp');
			$table->timestamps();
			$table->softDeletes();
			$table->primary('jurusan_sp_id');
			$table->foreign('sekolah_id')->references('sekolah_id')->on('sekolah')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('jurusan_id')->references('jurusan_id')->on('jurusan')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jurusan_sp', function (Blueprint $table) {
            $table->dropForeign(['jurusan_id']);
			$table->dropForeign(['sekolah_id']);
        });
        Schema::dropIfExists('jurusan_sp');
    }
}
