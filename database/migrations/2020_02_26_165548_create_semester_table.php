<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSemesterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('semester', function (Blueprint $table) {
            $table->string('semester_id', 5);
            $table->decimal('tahun_ajaran_id', 4, 0);
			$table->string('nama');
			$table->decimal('semester', 1, 0);
			$table->decimal('periode_aktif', 1, 0);
			$table->date('tanggal_mulai');
			$table->date('tanggal_selesai');
			$table->timestamps();
			$table->primary('semester_id');
			$table->foreign('tahun_ajaran_id')->references('tahun_ajaran_id')->on('tahun_ajaran')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('semester', function (Blueprint $table) {
            $table->dropForeign(['tahun_ajaran_id']);
        });
        Schema::dropIfExists('semester');
    }
}
