<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllFixturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('all_fixtures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('match_id')->nullable();
            $table->integer('competition_id')->nullable();
            $table->integer('match_day')->nullable();
            $table->string('status')->nullable();
            $table->string('utcDate')->nullable();
            $table->string('lastUpdated')->nullable();
            $table->integer('homeScore')->nullable();
            $table->integer('awayScore')->nullable();
            $table->integer('homeTeam_id')->nullable();
            $table->integer('awayTeam_id')->nullable();

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
        Schema::dropIfExists('all_fixtures');
    }
}
