<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMataPelajaranKurikulumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mapel_kur', function (Blueprint $table) {
            $table->smallInteger('kurikulum_id');
			$table->integer('mata_pelajaran_id');
			$table->decimal('tingkat', 2, 0);
			$table->decimal('jumlah_jam', 2, 0);
            $table->decimal('jumlah_jam_maksimum',2, 0);
			$table->decimal('wajib',1, 0);
			$table->decimal('sks',2, 0);
			$table->decimal('a_peminatan',1, 0);
			$table->string('area_kompetensi', 1);
			$table->string('gmp_id', 36)->nullable();
			$table->timestamps();
            $table->softDeletes();
            $table->timestamp('last_sync')->nullable();
			$table->foreign('kurikulum_id')->references('kurikulum_id')->on('kurikulum')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('mata_pelajaran_id')->references('mata_pelajaran_id')->on('mata_pelajaran')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->primary(['kurikulum_id', 'mata_pelajaran_id', 'tingkat']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mapel_kur', function (Blueprint $table) {
            $table->dropForeign(['kurikulum_id']);
			$table->dropForeign(['mata_pelajaran_id']);
        });
        Schema::dropIfExists('mapel_kur');
    }
}
