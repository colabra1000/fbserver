<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCompetitionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->string('a_flag')->nullable()->after('emblemUrl');
            $table->string('a_logo')->nullable()->after('emblemUrl');
            $table->string('a_season_end')->nullable()->after('emblemUrl');
            $table->string('a_season_start')->nullable()->after('emblemUrl');
            $table->string('a_league_name')->nullable()->after('emblemUrl');
            $table->string('a_country_name')->nullable()->after('emblemUrl');
            $table->integer('a_league_id')->unsigned()->nullable()->after('emblemUrl');
            $table->integer('a_season')->unsigned()->nullable()->after('emblemUrl');
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn('a_flag');
            $table->dropColumn('a_logo');
            $table->dropColumn('a_season_end');
            $table->dropColumn('a_season_start');
            $table->dropColumn('a_league_name');
            $table->dropColumn('a_country_name');
            $table->dropColumn('a_country_id');
            $table->dropColumn('a_season');
        });
    }
}
