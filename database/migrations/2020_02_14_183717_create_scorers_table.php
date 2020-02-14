<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScorersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scorers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('team_id')->nullable();
            $table->string('numberOfGoals')->nullable();
            $table->string('player_id')->nullable();
            $table->string('player_name')->nullable();
            $table->string('player_nationality')->nullable();
            $table->string('player_position')->nullable();
            $table->string('player_shirtNumber')->nullable();
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
        Schema::dropIfExists('scorers');
    }
}
