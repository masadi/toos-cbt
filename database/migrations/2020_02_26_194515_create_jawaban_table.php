<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJawabanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jawaban', function (Blueprint $table) {
            $table->uuid('jawaban_id');
            $table->uuid('bank_soal_id');
            $table->text('jawaban')->nullable();
            $table->decimal('jawaban_ke', 1, 0);
            $table->decimal('benar', 1, 0);
            $table->timestamps();
            $table->foreign('bank_soal_id')->references('bank_soal_id')->on('bank_soal')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->primary('jawaban_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jawaban', function (Blueprint $table) {
            $table->dropForeign(['bank_soal_id']);
        });
        Schema::dropIfExists('jawaban');
    }
}
