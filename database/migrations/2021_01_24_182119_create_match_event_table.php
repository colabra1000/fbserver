<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMatchEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('MatchEvents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('elapsed')->unsigned()->nullable();
            $table->integer('elapsed_plus')->unsigned()->nullable();
            $table->integer('team_id')->unsigned()->nullable();
            $table->string('player')->nullable();
            $table->string('assist')->nullable();
            $table->string('type')->nullable();
            $table->string('detail')->nullable();
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('MatchEvent');
    }
}
