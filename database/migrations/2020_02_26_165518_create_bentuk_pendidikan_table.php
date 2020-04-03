<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBentukPendidikanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bentuk_pendidikan', function (Blueprint $table) {
            $table->smallInteger('bentuk_pendidikan_id')->unsigned();
            $table->string('nama', 50);
            $table->decimal('jenjang_paud', 1, 0);
            $table->decimal('jenjang_tk', 1, 0);
            $table->decimal('jenjang_sd', 1, 0);
            $table->decimal('jenjang_smp', 1, 0);
            $table->decimal('jenjang_sma', 1, 0);
            $table->decimal('jenjang_tinggi', 1, 0);
            $table->string('direktorat_pembinaan', 40);
            $table->decimal('aktif', 1, 0);
            $table->timestamps();
            $table->primary('bentuk_pendidikan_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bentuk_pendidikan');
    }
}
