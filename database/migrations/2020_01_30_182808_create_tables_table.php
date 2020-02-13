<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('position');
            $table->integer('playedGames');
            $table->integer('won');
            $table->integer('draw');
            $table->integer('lost');
            $table->integer('points');
            $table->integer('goalsFor');
            $table->integer('goalsAgainst');
            $table->integer('goalDifference');
            $table->integer('team_id');
            $table->integer('competition_id');
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
        Schema::dropIfExists('tables');
    }
}
