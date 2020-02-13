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
        ]);
        DB::table('competitions')->insert([
            'name' => 'premierLeague',
            'id' => 2021,
        ]);
        DB::table('competitions')->insert([
            'name' => 'seriaA',
            'id' => 2019,
        ]);
        DB::table('competitions')->insert([
            'name' => 'ligue1',
            'id' => 2015,
        ]);
        DB::table('competitions')->insert([
            'name' => 'bundesLiga',
            'id' => 2002,
        ]);
    }
}
