<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Competition;
use App\Table;
use App\AllFixture;
use App\Team;
use App\Scorer;
use Football;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Console\Commands\UpdateAllFixtures;
use App\Http\Controllers\UpdateBringer;
use App\Http\Controllers\Fetcher;
use App\Http\Controllers\UpdateB;

use App\Http\Resources\Table as TableResource;
use App\Http\Resources\Team as TeamResource;
use App\Http\Resources\AllFixture as AllFixtureResource;

use \Transliterator;

class FbController extends Controller
{

    private $competitions;
    private $fetcher;

    private $countryLeagueMap = array(

        2021 => ["premier league", "england"],
        2019 => ["serie a", "italy"],
        2014 => ["primera division", "spain"],
        2015 => ["ligue 1", "france"],
        2002 => ["bundesliga 1", "germany"],
    );
    

    function __construct(){
       
        $this->fetcher = new Fetcher();
        $this->competitions = Competition::all();
    }

    function getTodayDate(){
        return date('Y-m-d', strtotime(now()));
    }

    private function checkIfMatchIsInProgress($datum){
        foreach($datum as $data){
            if($data->status == "IN_PLAY" || $data->status == "PAUSED" ){
                return true;
            }
        }
        return false;
    }



    private function getMatchesInProgress($datum){
        $recordKeepers = [];

        // foreach($datum as $data){
        //     if($data->status == "IN_PLAY" || $data->status == "PAUSED" ){
        //         array_push($recordKeepers, $data);
        //     }
        // }

        DB::beginTransaction();

            foreach($datum as $data){


                // $recordKeepers = AllFixtures::where(function($query){
                    
                // });


                $record = AllFixture::where("match_id", $data->id)->first();

                if($record != null){
                    array_push($recordKeepers, $record);
                }

                //////////////////// do stuff here tommorrow
            
            }
        
        DB::commit();

        foreach($recordKeepers as $recordKeeper){
            $matchTime - $recordKeeper->utcDate;
            if(time() > strtotime('-30 minutes', time())
            && $recordKeeper->firstUpdate != null
            ){

                //set first update to true;
                //fire event;
                //with this I can now get lineup

            }
        }

           


        echo("robsontagarian\n");
        echo($recordKeepers[3]->status."\n");
        echo(count($recordKeepers));
        // echo("\n".now()."\n".time());

        die();
        
        // return $recordKeepers;
    }


    //get matches for the day
    private function getTodayMatches(){

        //get matches from today's date to today
        $datum = UpdateBringer::updateBring(function(){
            return Football::getMatches(['competitions' => '', 'dateFrom' => $this->getTodayDate(), 'dateTo' => $this->getTodayDate()])->toArray();
        });
        return $datum;
    }


    //get current time
    function getTimeNow(){
        return strtotime(now());
    }

    function getAndFilterMatchesForToday(){
        $competitionFilter = function($arr){
            foreach($this->competitions as $competition){
                if($competition->id == $arr->competition->id){
                    return true;
                }
            }    
        };  
         //get all matches for today.
         $matchesForTodayObj = $this->getTodayMatches();

         //filter the matches by desired competitions.
         return array_filter($matchesForTodayObj, $competitionFilter);
    }


    //Main function that calls other functions
    function TodayUpdateDoer(){

        $this->initializeEverything();


        echo("\ndone and dusted\n");
        die();


        
        // if their are no fixtures in database then fetch features.
        if(AllFixture::all()->isEmpty()){
            echo "initializing all fixtures...\n";
            $this->fetcher->getCompetitionsFixtures();
        }

        // if their are no scorers in database then fetch the scorers
        if(Scorer::all()->isEmpty()){
            echo "initializing all scorers...\n";
            $this->fetcher->getScorers();
        }

        // if their are no tables in database the fetch the tables
        if(Table::all()->isEmpty()){
            echo "initializing all scorers...\n";
            $this->fetcher->getTables();
        }

        // if their are no teams in the database then fetch the teams
        if(Team::all()->isEmpty()){
            echo "initializing all Teams...\n";
            $this->fetcher->getLeagueTeams();
        }
      
        //just used for testing: count update loops
        $counter = 0;

        //duration of match
        $matchOffsetTime = '+180 minutes';

        $matchesForTodayObj = $this->getAndFilterMatchesForToday();

        //if no matches exists
        if(count($matchesForTodayObj) == 0){

            echo "No available Matches\n";

            //then get table
            $this->fetcher->getTables();

            echo "sleeping for 10 sec\n";
            sleep(10);

            //then get scorers
            $this->fetcher->getScorers();

            echo "sleeping for 10 sec\n";
            sleep(10);

            //then get league teams
            $this->fetcher->getLeagueTeams();

            echo "sleeping for 10 sec\n";
            sleep(10);

            //get competition fixtures.
            $this->fetcher->getCompetitionsFixtures();

            //stop execution of script and return.
            return;
        }

        //else if their are matches for the day...

        //get the last match for the day.
        $lastMatch = end($matchesForTodayObj);

        //get time match ends for the day.
        $lastMatchTime = strtotime($matchOffsetTime, strtotime($lastMatch->utcDate));
       
        //////filter the matches;
        
        echo "available matches\n";

        //while the last match for the day has not been played..
        while($this->getTimeNow() < $lastMatchTime){

            //start the long loop
            echo "sarting long loop\n";
            echo ++$counter."long loops passed\n";





            //loop through each match and check match time
            foreach($matchesForTodayObj as $data){

                //match time + match offset time; time to complete a match.
                $timeUpperBoundary = strtotime($matchOffsetTime, strtotime($data->utcDate));
                //match time
                $timeLowerBoundary = strtotime($data->utcDate);

                //if it a match is going on
                if($this->getTimeNow() > $timeLowerBoundary && $this->getTimeNow() < $timeUpperBoundary){

                    
                    $this->getMatchesInProgress($matchesForTodayObj);


                    //check if match is live
                    $liveMatches = $this->checkIfMatchIsInProgress($matchesForTodayObj);

                    
                    $matchesForTodayArr_prev = [];

                    
                        $tableArr_prev = [];
                        $scorersArr_prev = [];
                    
                        while($liveMatches == true){

                           


                        echo "live match loop\n";
                        
                        echo ++$counter." loop passed\n";

                        //a match has finished or is added

                        $matchesForToday = $this->getAndFilterMatchesForToday();

                        // $prevLiveMatches = $this->checkIfMatchIsInProgress($matchesForToday);
                        $matchesForTodayArr = [];
                        
                        
                        foreach($matchesForTodayObj as $dat){
            
                            $arr = [];
                            $arr['match_id'] = $dat->id;
                            $arr['match_day'] = $dat->matchday;
                            $arr['competition_id'] = $dat->competition->id;
                            $arr['status'] = $dat->status;
                            $arr['utcDate'] = $dat->utcDate;
                            $arr['lastUpdated'] = $dat->lastUpdated;
                            $arr['homeScore'] = $dat->score->fullTime->homeTeam;
                            $arr['awayScore'] = $dat->score->fullTime->awayTeam;
                            $arr['homeTeam_id'] = $dat->homeTeam->id;
                            $arr['awayTeam_id'] = $dat->awayTeam->id;

                            // DB::table('all_fixtures')->where('match_id', $dat->id)->update($arr); 
                            array_push($matchesForTodayArr, $arr);
                            
                        }
                        
                        echo "comparing\n";
                        $compareMonitor = false;                        
                        $compareMonitor = $this->fetcher->comparor($matchesForTodayArr, $matchesForTodayArr_prev, "match_id");
                            
                        if(!$compareMonitor){

                            echo "changed detected \n";
                            foreach($matchesForTodayArr as $arr){
                                DB::table('all_fixtures')->where('match_id', $arr['match_id'])->update($arr); 
                            }

                            echo "m_database updated\n"; 

                            $fetcheObject = $this->fetcher->getTables($tableArr_prev, false);
                            $tableArr_prev = $fetcheObject->getArr();
                            if($fetcheObject->hasArrayChanged()){
                                
                                sleep(2);
                                echo "table database updated\n";
                                //update the database;
                                Table::truncate();
                                DB::table('tables')->insert($tableArr_prev);
                            }else{
                                echo "tables database not changed";
                            }


                          
                            $fetcheObject = $this->fetcher->getScorers($scorersArr_prev, false);  
                            $scorersArr_prev = $fetcheObject->getArr();
                            if($fetcheObject->hasArrayChanged()){
                                

                                sleep(2);
                                echo "scorers database updated\n";
                                // $this->fetcher->getScorers();

                                Scorer::truncate();
                                DB::table('scorers')->insert($scorersArr_prev);

                            }else{
                                echo "scorers database not changed";
                            }
                           
                            //trigger broadcast
                        }else{
                            echo("no changes detected\n");
                        }

                        $matchesForTodayArr_prev = $matchesForTodayArr;


                        echo "sleeping for 16 sec\n"; 
                        sleep(16);
                        //end of loop for checking for live matches.
                    }
                }
            }//has checked for all available matches.
            sleep(8);

            //end of main loop.
        }

         //get table
         $this->fetcher->getTables();

         //get league teams
         $this->fetcher->getLeagueTeams();

         //get competition fixtures
         $this->fetcher->getCompetitionsFixtures();

         //get competition scorers
         $this->fetcher->getScorers();


    }  


    function putUpToDate(){
        $this->fetcher->getCompetitionsFixtures();
        sleep(3);
        $this->fetcher->getScorers();
        sleep(3);
        $this->fetcher->getTables();
        sleep(3);
        $this->fetcher->getLeagueTeams();
    }




   

    function testo(){
       
        // $updateB = new UpdateB();
        // $updateB->testEvent();
        $this->fetcher->getScorers();
    
    }


    function initializeEverything(){



        $key = 587323;
        //match id



        $datum = UpdateBringer::updateBring(function ($key){

            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://v2.api-football.com/fixtures/id/'.$key, ['headers' => [
                'X-RapidAPI-Key' => '73afa7925cb7d97a8d57d07706957193',
                ]
            ]);
            return json_decode($response->getBody());
            
        }, $key);

        //fixtures
        //firsthalf start, secondhalf start,






        return;



        $aleague = Competition::all();
        $localFixtures = AllFixture::all();



        foreach($aleague as $val){

            $key = $val->a_league_id;
            // echo 'wand'. $key. "  ";

            $datum = UpdateBringer::updateBring(function ($key){

                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', 'https://v2.api-football.com/fixtures/league/'.$key, ['headers' => [
                    'X-RapidAPI-Key' => '73afa7925cb7d97a8d57d07706957193',
                    ]
                ]);
                return json_decode($response->getBody());
                
            }, $key);


            DB::beginTransaction();
    
            foreach($datum->api->fixtures as $fixture){

                $homeTeamId = $fixture->homeTeam->team_id;
                $awayTeamId = $fixture->awayTeam->team_id;

                $a_home_team_id = DB::table('teams')->where("a_team_id", $homeTeamId)->first();
                $a_away_team_id = DB::table('teams')->where("a_team_id", $awayTeamId)->first();

                if($a_home_team_id == null || $a_away_team_id == null){
                    continue;
                }


                DB::table('all_fixtures')->where("homeTeam_id", $a_home_team_id->id)->where("awayTeam_id", $a_away_team_id->id)
                ->update(['a_fixture_id' => $fixture->fixture_id,
                'a_refree' => $fixture->referee,
                'a_venue' => $fixture->venue,
                'a_elapsed' => $fixture->elapsed,
                'a_secondHalf_start' => $fixture->secondHalfStart,
                'a_homeTeam_id' => $fixture->homeTeam->team_id,
                'a_awayTeam_id' => $fixture->awayTeam->team_id,
                'a_firstHalf_start' => $fixture->firstHalfStart],);


               
                // foreach($localFixtures as $lf){
                //     // $fixtureId = $fixture->fixture_id;
                //     $homeTeamId = $fixture->homeTeam->team_id;
                //     $awayTeamId = $fixture->awayTeam->team_id;


                //     $homeTeamIdLocal = $lf->homeTeam_id;
                //     $awayTeamIdLocal = $lf->awayTeam_id;

                   

        
                    // if(strcasecmp(trim($homeTeamId), trim($homeTeamIdLocal)) == 0 && strcasecmp(trim($awayTeamId), trim($awayTeamIdLocal)) == 0){
    
                    //     // echo "| ". "(".$str1. " - ". $str2. ") "."(".$str3. " - ". $str. ") " ." | --- ";
                      
                    //     DB::table('all_fixtures')
                    //         ->where()
                    //         ->update(['a_fixture_id' => $fixture->fixture_id,
                    //         'a_refree' => $fixture->referee,
                    //         'a_venue' => $fixture->venue,
                    //         'a_elapsed' => $fixture->elapsed,
                    //         'a_secondHalf_start' => $fixture->secondHalfStart,
                    //         'a_firstHalf_start' => $fixture->firstHalfStart],);

                    // }
    
                // }

           
    
            }
    
            DB::commit();
            
        }   



        echo("done");






        return;


        $localTeam = Team::all();
        $rule = 'NFD; [:Nonspacing Mark:] Remove; NFC';
    
        $myTrans = Transliterator::create($rule);



        $aleague = Competition::all();


        // $stadiums = [];

        // foreach($aleague as $l){

        // }


        
        // $str1 = $myTrans->transliterate($team->venue_name);
        // $str2 = $myTrans->transliterate($ll->venue);

        // $str3 = $myTrans->transliterate($team->name);
        // $str4 = $myTrans->transliterate($ll->name);

        
        // $pattern = ["/-/","/\bfc\b/i", "/\bcf\b/i", "/\bsv\b/i", "/\bdsc\b/i"
        //             , "/\brc\b/i", "/\bde\b/i", "/\bsd\b/i", "/\bud\b/i"
        //             , "/\bogc\b/i", "/\bbc\b/i"];
        // $replacement = [" ", ""];

        // $str1 = str_replace("-"," ", $str1);
        // $str2 = str_replace("-"," ", $str2);

        // $str3 = preg_replace( $pattern, $replacement, $str3);
        // $str4 = preg_replace( $pattern, $replacement, $str4);
  







        // $stad = [];
        // $stad01 = [];
        // $rr = true;





        foreach($aleague as $val){

            $key = $val->a_league_id;
            // echo 'wand'. $key. "  ";

            $datum = UpdateBringer::updateBring(function ($key){

                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', 'https://v2.api-football.com/teams/league/'.$key, ['headers' => [
                    'X-RapidAPI-Key' => '73afa7925cb7d97a8d57d07706957193',
                    ]
                ]);
                return json_decode($response->getBody());
                
            }, $key);        
    
           
    

            // $outliers = [
            //     'Weserstadion' => 'wohninvest WESERSTADION',
            //     'SchücoArena Kunstrasenplatz' => 'SchücoArena',
            //     'Estadio Alfredo Di Stéfano' => 'Estadio Santiago Bernabéu',
            //     'Estadio Municipal de Anoeta' => 'Reale Arena',
            //     'Estadio de Balaídos' => 'Abanca-Balaídos',
            //     'Stade de Nice'=>'Allianz Riviera',
            //     'Stade Louis II.' => 
            //     ]



           
           
            DB::beginTransaction();
    
            foreach($datum->api->teams as $team){


                // $stro1 = $myTrans->transliterate($team->venue_name);
                // $stro3 = $myTrans->transliterate($team->name);
                // $pattern = ["/-/","/\bfc\b/i", "/\bcf\b/i", "/\bsv\b/i", "/\bdsc\b/i"
                // , "/\brc\b/i", "/\bde\b/i", "/\bsd\b/i", "/\bud\b/i"
                // , "/\bogc\b/i", "/\bbc\b/i"];
                // $replacement = [" ", ""];

                // $stro1 = str_replace("-"," ", $stro1);

                // $stro3 = preg_replace( $pattern, $replacement, $stro3);

                // $stad += [$stro1.">" => $stro3];






                // $stadiums += [$team->name => $team->venue_name];
    
               
                foreach($localTeam as $ll){
    
                   

                    $str1 = $myTrans->transliterate($team->venue_name);
                    $str2 = $myTrans->transliterate($ll->venue);

                    $str3 = $myTrans->transliterate($team->name);
                    $str4 = $myTrans->transliterate($ll->name);

                    
                    $pattern = ["/-/", "/\bfc\b/i", "/\bcf\b/i", "/\bsv\b/i", "/\bdsc\b/i"
                                , "/\brc\b/i", "/\bde\b/i", "/\bsd\b/i", "/\bud\b/i"
                                , "/\bogc\b/i", "/\bbc\b/i", "/\bfutbol\b/i", "/\bas\b/i"];
                    $replacement = [" ",""];


                    $pattern2 = ["/\s+/"];
                    $replacement2 = [" "];
    
                    $str1 = str_replace("-"," ", $str1);
                    $str2 = str_replace("-"," ", $str2);

                    $str3 = preg_replace( $pattern, $replacement, $str3);
                    $str4 = preg_replace( $pattern, $replacement, $str4);
              
                    $str3 = preg_replace( $pattern2, $replacement2, $str3);
                    $str4 = preg_replace( $pattern2, $replacement2, $str4);
              

                    // if($rr == true){
                        
                    //     $stad01 += [$str2 => $str4];
                    // }

        
                    if(strcasecmp(trim($str1), trim($str2)) == 0 || strcasecmp(trim($str3), trim($str4)) == 0){
    
                        // echo "| ". "(".$str1. " - ". $str2. ") "."(".$str3. " - ". $str. ") " ." | --- ";
    
                        DB::table('teams')
                            ->where('id', $ll->id)
                            ->update(['a_team_id' => $team->team_id,
                                        'a_capacity' => $team->venue_capacity,
                                        'a_city' => $team->venue_city,
                                        'a_address' => $team->venue_address,
                                        'a_venue_name' => $team->venue_name,
                                        'a_country' => $team->country,
                                        'a_league_id' => $key,
                                        'a_logo' => $team->logo],);
                    }
    
                }

                // $rr = false;
    
            }
    
            DB::commit();
            
        }

        // echo ("done");

        // echo($stad);
        // echo($stad01);

        // $stad += $stad01;

        // dd($stad);



      



        // dd($datum);
       
        // dd($rob);

        return;
      
        $idd = 0;

        $datum = UpdateBringer::updateBring(function ($idd){

            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://v2.api-football.com/leagues/current/', ['headers' => [
                'X-RapidAPI-Key' => '73afa7925cb7d97a8d57d07706957193',
                ]
            ]);
            return json_decode($response->getBody());
            
        }, $idd);


        foreach($this->countryLeagueMap as $key=>$val){


            foreach($datum->api->leagues as $league){

                if(strcasecmp($league->country, $val[1]) == 0 &&
                strcasecmp($league->name, $val[0]) == 0){

                    $competition = Competition::where('id', $key)->first();
                    $competition->a_country_name = $league->country; 
                    $competition->a_league_id =  $league->league_id;
                    $competition->a_league_name =  $league->name;
                    $competition->a_season =  $league->season;
                    $competition->a_season_start =  $league->season_start;
                    $competition->a_season_end =  $league->season_end;
                    $competition->a_logo =  $league->logo;
                    $competition->a_flag =  $league->flag;
                    
                    $competition->save();

                }
            }

        }








       

        dd($datum);

        return;




         $datum = UpdateBringer::updateBring(function ($idd){

            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://api.football-data.org/v2/competitions/'.'2021'.'/scorers', ['headers' => [
                'X-Auth-token' => '30d8aa28ae5b4a60b541cf3ac0e5b818',
                ]
            ]);
            return json_decode($response->getBody());
            
        }, $idd);




    }


}
