<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTahunAjaranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->decimal('tahun_ajaran_id', 4, 0);
            $table->string('nama', 10);
			$table->decimal('periode_aktif', 1, 0);
			$table->date('tanggal_mulai');
			$table->date('tanggal_selesai');
			$table->timestamps();
			$table->primary('tahun_ajaran_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tahun_ajaran');
    }
}
