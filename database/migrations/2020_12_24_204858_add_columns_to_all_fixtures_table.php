<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToAllFixturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('all_Fixtures', function (Blueprint $table) {
            $table->boolean('thirdUpdate')->nullable()->after('awayTeam_id');
            $table->boolean('secondUpdate')->nullable()->after('awayTeam_id');
            $table->boolean('firstUpdate')->nullable()->after('awayTeam_id');
            $table->string('a_refree')->nullable()->after('awayTeam_id');
            $table->string('a_venue')->nullable()->after('awayTeam_id');
            $table->string('a_elapsed')->nullable()->after('awayTeam_id');
            $table->string('a_secondHalf_start')->nullable()->after('awayTeam_id');
            $table->string('a_firstHalf_start')->nullable()->after('awayTeam_id');
            $table->string('a_fixture_id')->nullable()->after('awayTeam_id');
            $table->string('a_homeTeam_id')->nullable()->after('awayTeam_id');
            $table->string('a_awayTeam_id')->nullable()->after('awayTeam_id');
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('all_Fixtures', function (Blueprint $table) {
            $table->dropColumn('a_refree');
            $table->dropColumn('a_venue');
            $table->dropColumn('a_elapsed');
            $table->dropColumn('a_secondHalf_start');
            $table->dropColumn('a_firstHalf_start');
            $table->dropColumn('a_fixture_id');
            $table->dropColumn('a_homeTeam_id');
            $table->dropColumn('a_awayTeam_id');

            $table->dropColumn('firstUpdate');
            $table->dropColumn('secondUpdate');
            $table->dropColumn('thirdUpdate');
           
        });
    }
}
