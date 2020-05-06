<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJenjangPendidikanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jenjang_pendidikan', function (Blueprint $table) {
            $table->decimal('jenjang_pendidikan_id', 2, 0);
            $table->string('nama', 25);
            $table->decimal('jenjang_lembaga', 1, 0);
			$table->decimal('jenjang_orang', 1, 0);
			$table->timestamps();
			$table->primary('jenjang_pendidikan_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jenjang_pendidikan');
    }
}
