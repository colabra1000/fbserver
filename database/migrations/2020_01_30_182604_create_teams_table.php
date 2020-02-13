<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->string('name')->nullable();
            $table->string('competition_id')->nullable();
            $table->string('shortName')->nullable();
            $table->string('tla')->nullable();
            $table->string('crestUrl')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->integer('founded')->nullable();
            $table->string('venue')->nullable();
            $table->string('lastUpdated')->nullable();
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
        Schema::dropIfExists('teams');
    }
}
