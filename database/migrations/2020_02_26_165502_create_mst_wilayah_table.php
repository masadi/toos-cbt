<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstWilayahTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('level_wilayah', function (Blueprint $table) {
			$table->smallInteger('id_level_wilayah');
			$table->string('level_wilayah', 15);
			$table->timestamps();
			$table->softDeletes();
			$table->primary('id_level_wilayah');
        });
		Schema::create('negara', function (Blueprint $table) {
			$table->string('negara_id', 2);
			$table->string('nama', 45);
			$table->decimal('luar_negeri', 1, 0);
			$table->timestamps();
			$table->softDeletes();
			$table->primary('negara_id');
        });
        Schema::create('mst_wilayah', function (Blueprint $table) {
			$table->string('kode_wilayah', 8);
			$table->string('nama', 60);
			$table->smallInteger('id_level_wilayah');
			$table->string('mst_kode_wilayah', 8)->nullable();
			$table->string('negara_id',2);
			$table->string('asal_wilayah', 8)->nullable();
			$table->string('kode_bps', 7)->nullable();
			$table->string('kode_dagri', 7)->nullable();
			$table->string('kode_keu', 10)->nullable();
			$table->timestamps();
            $table->softDeletes();
            $table->primary('kode_wilayah');
			$table->foreign('id_level_wilayah')->references('id_level_wilayah')->on('level_wilayah')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('negara_id')->references('negara_id')->on('negara')->onUpdate('RESTRICT')->onDelete('RESTRICT');
            $table->foreign('mst_kode_wilayah')->references('kode_wilayah')->on('mst_wilayah')->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mst_wilayah', function (Blueprint $table) {
            $table->dropForeign(['id_level_wilayah']);
            $table->dropForeign(['negara_id']);
            $table->dropForeign(['mst_kode_wilayah']);
        });
        Schema::dropIfExists('level_wilayah');
        Schema::dropIfExists('negara');
        Schema::dropIfExists('mst_wilayah');
    }
}
