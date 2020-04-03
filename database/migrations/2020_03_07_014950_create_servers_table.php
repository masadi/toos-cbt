<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->uuid('server_id');
            $table->uuid('sekolah_id');
			$table->string('id_server', 13);
            $table->string('sn', 36)->nullable();
            $table->string('password', 5);
            $table->decimal('status', 1, 0)->nullable()->default(0);
            $table->timestamps();
            $table->primary('server_id');
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
        Schema::table('servers', function (Blueprint $table) {
            $table->dropForeign(['sekolah_id']);
        });
        Schema::dropIfExists('servers');
    }
}
