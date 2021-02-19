<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineUpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_ups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('match_id')->unsigned()->nullable();
            $table->integer('team_id')->unsigned()->nullable();
            $table->integer('number')->unsigned()->nullable();
            $table->integer('minutes_played')->unsigned()->nullable();
            $table->integer('rating')->unsigned()->nullable();
            $table->integer('player_id')->unsigned()->nullable();
            $table->string('player')->nullable();
            $table->string('position')->nullable();
            $table->string('subbed_for')->nullable();
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
        Schema::dropIfExists('line_ups');
    }
}
