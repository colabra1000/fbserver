<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('match_id')->unsigned()->nullable();
            $table->integer('shot_on_goal_home')->unsigned()->nullable();
            $table->integer('shot_on_goal_away')->unsigned()->nullable();
            $table->integer('shot_off_goal_home')->unsigned()->nullable();
            $table->integer('shot_off_goal_away')->unsigned()->nullable();
            $table->integer('total_shots_home')->unsigned()->nullable();
            $table->integer('total_shots_away')->unsigned()->nullable();
            $table->integer('blocked_shots_home')->unsigned()->nullable();
            $table->integer('blocked_shots_away')->unsigned()->nullable();
            $table->integer('shot_inside_box_home')->unsigned()->nullable();
            $table->integer('shot_inside_box_away')->unsigned()->nullable();
            $table->integer('shot_outside_box_home')->unsigned()->nullable();
            $table->integer('shot_outside_box_away')->unsigned()->nullable();
            $table->integer('fouls_home')->unsigned()->nullable();
            $table->integer('fouls_away')->unsigned()->nullable();
            $table->integer('corner_kicks_home')->unsigned()->nullable();
            $table->integer('corner_kicks_away')->unsigned()->nullable();
            $table->integer('offsides_home')->unsigned()->nullable();
            $table->integer('offsides_away')->unsigned()->nullable();
            $table->integer('ball_possession_home')->unsigned()->nullable();
            $table->integer('ball_possession_away')->unsigned()->nullable();
            $table->integer('yellow_cards_home')->unsigned()->nullable();
            $table->integer('yellow_cards_away')->unsigned()->nullable();
            $table->integer('goalkeeper_saves_home')->unsigned()->nullable();
            $table->integer('goalkeeper_saves_away')->unsigned()->nullable();
            $table->integer('total_passes_home')->unsigned()->nullable();
            $table->integer('total_passes_away')->unsigned()->nullable();
            $table->integer('pass_completed_home')->unsigned()->nullable();
            $table->integer('pass_completed_away')->unsigned()->nullable();
            $table->integer('pass_accuracy_home')->unsigned()->nullable();
            $table->integer('pass_accuracy_away')->unsigned()->nullable();
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
        Schema::dropIfExists('statistics');
    }
}
