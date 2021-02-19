<?php

use Illuminate\Database\Seeder;

class CompetitionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('competitions')->insert([
            'name' => 'laLiga',
            'id' => 2014,
            'currentMatchDay' => 1,
        ]);
        
        DB::table('competitions')->insert([
            'name' => 'premierLeague',
            'id' => 2021,
            'currentMatchDay' => 1,
        ]);
        DB::table('competitions')->insert([
            'name' => 'seriaA',
            'id' => 2019,
            'currentMatchDay' => 1,
        ]);
        DB::table('competitions')->insert([
            'name' => 'ligue1',
            'id' => 2015,
            'currentMatchDay' => 1,
        ]);
        DB::table('competitions')->insert([
            'name' => 'bundesLiga',
            'id' => 2002,
            'currentMatchDay' => 1,
        ]);
    }
}
