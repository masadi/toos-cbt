<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnggotaRombelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anggota_rombel', function (Blueprint $table) {
            $table->uuid('anggota_rombel_id');
			$table->uuid('sekolah_id');
			$table->string('semester_id', 5);
			$table->uuid('rombongan_belajar_id');
            $table->uuid('peserta_didik_id');
            $table->string('nama_peserta_didik');
			$table->timestamps();
			$table->softDeletes();
			$table->primary('anggota_rombel_id');
            $table->foreign('sekolah_id')->references('sekolah_id')->on('sekolah')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('semester_id')->references('semester_id')->on('semester')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('rombongan_belajar_id')->references('rombongan_belajar_id')->on('rombongan_belajar')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('peserta_didik_id')->references('peserta_didik_id')->on('peserta_didik')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('anggota_rombel', function (Blueprint $table) {
            $table->dropForeign(['sekolah_id']);
            $table->dropForeign(['semester_id']);
            $table->dropForeign(['rombongan_belajar_id']);
			$table->dropForeign(['peserta_didik_id']);
        });
        Schema::dropIfExists('anggota_rombel');
    }
}
