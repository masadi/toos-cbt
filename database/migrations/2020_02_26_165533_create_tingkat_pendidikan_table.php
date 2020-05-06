<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTingkatPendidikanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tingkat_pendidikan', function (Blueprint $table) {
            $table->decimal('tingkat_pendidikan_id', 2, 0);
            $table->string('kode', 5);
            $table->string('nama', 20);
            $table->decimal('jenjang_pendidikan_id', 2, 0);
			$table->timestamps();
            $table->primary('tingkat_pendidikan_id');
            $table->foreign('jenjang_pendidikan_id')->references('jenjang_pendidikan_id')->on('jenjang_pendidikan')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tingkat_pendidikan', function (Blueprint $table) {
            $table->dropForeign(['jenjang_pendidikan_id']);
        });
        Schema::dropIfExists('tingkat_pendidikan');
    }
}
