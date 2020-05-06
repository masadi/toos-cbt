<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJurusanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jurusan', function (Blueprint $table) {
            $table->string('jurusan_id', 25);
			$table->string('nama_jurusan', 100);
			$table->decimal('untuk_sma', 1, 0);
			$table->decimal('untuk_smk', 1, 0);
			$table->decimal('untuk_pt', 1, 0);
			$table->decimal('untuk_slb', 1, 0);
			$table->decimal('untuk_smklb', 1, 0);
			$table->decimal('jenjang_pendidikan_id', 1, 0)->nullable();
			$table->string('jurusan_induk', 25)->nullable();
			$table->string('level_bidang_id', 5);
			$table->timestamps();
			$table->softDeletes();
			$table->primary('jurusan_id');
			$table->foreign('jurusan_induk')->references('jurusan_id')->on('jurusan')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jurusan', function (Blueprint $table) {
            $table->dropForeign(['jurusan_induk']);
        });
        Schema::dropIfExists('jurusan');
    }
}
