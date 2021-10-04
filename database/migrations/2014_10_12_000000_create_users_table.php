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
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
            $table->softDeletes();

            $table->unsignedMediumInteger('points')->default(0);
            $table->unsignedSmallInteger("nbSessions")->default(0);
            $table->float("efficiency")->default(0);
            $table->unsignedMediumInteger("nbKills")->default(0);
            $table->float("averageKills")->default(0);
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
