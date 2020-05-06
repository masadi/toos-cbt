<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_exams', function (Blueprint $table) {
            $table->uuid('user_exam_id');
            $table->uuid('exam_id');
            $table->uuid('anggota_rombel_id')->nullable();
            $table->uuid('ptk_id')->nullable();
            $table->time('sisa_waktu')->nullable();
            $table->decimal('status_ujian', 1, 0)->nullable();
            $table->decimal('status_upload', 1, 0)->nullable();
            $table->timestamps();
            $table->foreign('exam_id')->references('exam_id')->on('exams')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('anggota_rombel_id')->references('anggota_rombel_id')->on('anggota_rombel')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('ptk_id')->references('ptk_id')->on('ptk')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->primary('user_exam_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_exams', function (Blueprint $table) {
            $table->dropForeign(['exam_id']);
            $table->dropForeign(['anggota_rombel_id']);
            $table->dropForeign(['ptk_id']);
        });
        Schema::dropIfExists('user_exams');
    }
}
