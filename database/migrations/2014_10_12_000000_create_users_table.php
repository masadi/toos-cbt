<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            //$table->bigIncrements('id');
            $table->uuid('user_id');
            $table->uuid('sekolah_id')->nullable();
            $table->uuid('ptk_id')->nullable();
            $table->uuid('peserta_didik_id')->nullable();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('timezone', 60);
            $table->string('menuroles');
            $table->rememberToken();
            $table->string('photo')->nullable();
            $table->boolean('logout')->nullable()->default(TRUE);
            $table->timestamps();
            $table->softDeletes();
            $table->primary('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
