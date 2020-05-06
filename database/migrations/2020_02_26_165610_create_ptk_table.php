<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePtkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ptk', function (Blueprint $table) {
            $table->uuid('ptk_id');
			$table->uuid('sekolah_id');
			$table->string('nama');
			$table->string('nuptk')->nullable();
			$table->string('nip')->nullable();
			$table->string('jenis_kelamin');
			$table->string('tempat_lahir');
			$table->date('tanggal_lahir');
			$table->string('nik', 16)->nullable();
			$table->integer('agama_id');
			$table->string('alamat')->nullable();
			$table->string('no_hp')->nullable();
			$table->string('email')->nullable();
			$table->string('photo')->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->primary('ptk_id');
			$table->foreign('sekolah_id')->references('sekolah_id')->on('sekolah')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ptk', function (Blueprint $table) {
            $table->dropForeign(['sekolah_id']);
        });
        Schema::dropIfExists('ptk');
    }
}
