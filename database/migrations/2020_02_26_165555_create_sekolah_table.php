<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSekolahTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sekolah', function (Blueprint $table) {
            $table->uuid('sekolah_id');
			$table->string('npsn');
			$table->string('nama');
			$table->smallInteger('bentuk_pendidikan_id')->unsigned();
			$table->string('alamat')->nullable();
			$table->string('desa_kelurahan')->nullable();
			$table->string('kecamatan')->nullable();
			$table->string('kabupaten')->nullable();
			$table->string('provinsi')->nullable();
			$table->string('kode_wilayah')->nullable();
			$table->string('kode_pos')->nullable();
			$table->string('no_telp')->nullable();
			$table->string('email')->nullable();
			$table->string('website')->nullable();
			$table->integer('status_sekolah');
            $table->string('logo_sekolah')->nullable();
            $table->string('lisensi', 10)->nullable();
			$table->timestamps();
			$table->softDeletes();
            $table->primary('sekolah_id');
            $table->foreign('bentuk_pendidikan_id')->references('bentuk_pendidikan_id')->on('bentuk_pendidikan')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('kode_wilayah')->references('kode_wilayah')->on('mst_wilayah')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->dropForeign(['bentuk_pendidikan_id']);
            $table->dropForeign(['kode_wilayah']);
        });
        Schema::dropIfExists('sekolah');
    }
}
