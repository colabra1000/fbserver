<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('a_logo')->nullable()->after('venue');
            $table->string('a_country')->nullable()->after('venue');
            $table->string('a_venue_name')->nullable()->after('venue');
            $table->string('a_address')->nullable()->after('venue');
            $table->string('a_city')->nullable()->after('venue');
            $table->integer('a_capacity')->unsigned()->nullable()->after('venue');
            $table->integer('a_league_id')->unsigned()->nullable()->after('venue');
            $table->integer('a_team_id')->unsigned()->nullable()->after('venue');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('a_logo');
            $table->dropColumn('a_country');
            $table->dropColumn('a_venue_name');
            $table->dropColumn('a_address');
            $table->dropColumn('a_city');
            $table->dropColumn('a_capacity');
            $table->dropColumn('a_league_id');
            $table->dropColumn('a_team_id');
        });
    }
}
