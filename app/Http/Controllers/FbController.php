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

use App\Http\Resources\Table as TableResource;
use App\Http\Resources\Team as TeamResource;
use App\Http\Resources\AllFixture as AllFixtureResource;

class FbController extends Controller
{

    private $competitions;
    private $todayDate;
    private $returnError;
    private $returnErrorCount;
    

    function __construct(){
      
        //get saved competition from model
        $this->competitions = Competition::all();

        //get today's date.
        $this->todayDate = date('Y-m-d', strtotime(now()));

        //variables used later on.
        $this->returnError = false;
        //used for number of retries
        $this->returnErrorCount = 0;
    }

    function updateBringer($returner, $arg = null){
         //loop through the competition and get each table
        
            //set numbers to retry to 0
             $returnErrorCount = 0;
             do{
                 $clientError = false;
                 $returnErrorCount ++;
 
                 try{
                    
                     $datum = $returner($arg);
                    
                 }catch(\GuzzleHttp\Exception\ConnectException $e){

                   
                    echo "cant update table\n";
                    $clientError = true;
                 
                }catch(\GuzzleHttp\Exception\ClientException $e){
 
                    echo "cant update table\n";    
                    $clientError  = true;

                 }catch(\GuzzleHttp\Exception\RequestException $e){
                    echo "this unknown curl error\n";    
                    $clientError  = true;
                 }

                 echo "sleepin 3 sec\n"; 
                 sleep(3);
 
                //if upto 5 retries, end
                if($returnErrorCount >= 5){
                   
                    die('errors contact admnistrator');
                }
 
            }while($clientError == true);

            return $datum;
    }


    //Gets all league Tables
    function getTables(){

        //array for query mass insertion
        $tableArr = [];

        //loop through the competition and get each table
        foreach($this->competitions as $competition){

            // $datum = $this->updateBringer(function($competition){
            //     return Football::getLeagueStandings($competition->id);
            // }, $competition);


            //get datum
            $datum = $this->updateBringer(function($competition){

                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', 'https://api.football-data.org/v2/competitions/'.$competition->id.'/standings', ['headers' => [
                    'X-Auth-token' => '30d8aa28ae5b4a60b541cf3ac0e5b818',
                    ]
                ]);
                return json_decode($response->getBody());
                
            }, $competition);

            
            $currentMatchday = $datum->season->currentMatchday;
            Competition::where('id', $competition->id)->update(['currentMatchDay' => intval($currentMatchday)]);



            foreach($datum->standings[0]->table as $data){
                $arr = [];
                $arr['position'] = $data->position;
                $arr['team_id'] = $data->team->id;
                $arr['competition_id'] = $competition->id;
                $arr['playedGames'] = $data->playedGames;
                $arr['won'] = $data->won;
                $arr['draw'] = $data->draw;
                $arr['lost'] = $data->lost;
                $arr['points'] = $data->points;
                $arr['goalsFor'] = $data->goalsFor;
                $arr['goalsAgainst'] = $data->goalsAgainst;
                $arr['goalDifference'] = $data->goalDifference;
                array_push($tableArr, $arr); 
            }

            echo "sleeping for 5 sec\n";
            sleep(5);

        }

        //clear all data from the database table
        Table::truncate();

        //insert tables to database
        DB::table('tables')->insert($tableArr);

        echo "tables fetched and updated\n";
       
   }

   function getCompetitionsFixtures(){

         //array for query mass insertion
         $tableArr = [];

         //loop through the competition and get each table
         foreach($this->competitions as $competition){  

            //fetch api
            $datum = $this->updateBringer(function($competition){
                return Football::getLeagueMatches($competition->id);
            }, $competition);
            
            foreach($datum as $data){
        
                $arr = [];
                $arr['match_id'] = $data->id;
                $arr['match_day'] = $data->matchday;
                $arr['competition_id'] = $competition->id;
                $arr['status'] = $data->status;
                $arr['utcDate'] = $data->utcDate;
                $arr['lastUpdated'] = $data->lastUpdated;
                $arr['homeScore'] = $data->score->fullTime->homeTeam;
                $arr['awayScore'] = $data->score->fullTime->awayTeam;
                $arr['homeTeam_id'] = $data->homeTeam->id;
                $arr['awayTeam_id'] = $data->awayTeam->id;
                array_push($tableArr, $arr); 
            }

            echo "sleeping for 5 sec\n";
            sleep(5);
        }
 
        //clear all data from the database table
        AllFixture::truncate();
 
        //insert tables to database
        DB::table('all_fixtures')->insert($tableArr);

        echo "all competitions fixtures updated\n";
        
   }


   function getLeagueTeams(){
        //get all competition
        //array for query mass insertion
        $tableArr = [];

        //loop through the competition and get each table
        foreach($this->competitions as $competition){
          
            //fetch teams from API
            $datum = $this->updateBringer(function($competition){
                return Football::getLeagueTeams($competition->id);
            }, $competition);

            //get all datas into array for database mass insertion
            foreach($datum as $data){
        
                $arr = [];
                $arr['id'] = $data->id;
                $arr['name'] = $data->name;
                $arr['competition_id'] = $competition->id;
                $arr['shortName'] = $data->shortName;
                $arr['tla'] = $data->tla;
                $arr['crestUrl'] = $data->crestUrl;
                $arr['address'] = $data->address;
                $arr['email'] = $data->email;
                $arr['founded'] = $data->founded;
                $arr['venue'] = $data->venue;
                $arr['lastUpdated'] = $data->lastUpdated;
                array_push($tableArr, $arr); 
            }

            echo "sleeping for 5 sec\n";
            sleep(5);
        }

        //clear all data from the database table
        Team::truncate();

        //insert tables to database
        DB::table('teams')->insert($tableArr);

        echo "Leagues Team updated\n";
    }

    //check for live matches
    private function checkForLiveMatches($datum){
       
        $datum = array_filter($datum, function($data){
            if($data->status == "IN_PLAY" || $data->status == "PAUSED" ){
                return true;
            }
        });

        //create a class to return;
        $obj = new \stdClass();
        
        //if their are any..
        if (count($datum) > 0){

            $obj->isLiveMatchAvailable = true;
            $obj->datum = $datum;
            $obj->countMatches = count($datum);

            return $obj;             
            
        }

        //else
        
            $obj->isLiveMatchAvailable = false;
            $obj->datum = null;
            $obj->countMatches = 0;

            return $obj;    
        
    }

   

    //get matches for the day
    private function getTodayMatches(){

        //get matches from today's date to today
        $datum = $this->updateBringer(function(){
            return Football::getMatches(['competitions' => '', 'dateFrom' => $this->todayDate, 'dateTo' => $this->todayDate])->toArray();
        });
       
        return $datum;
    }

    private function getScorers(){
        
         //array for query mass insertion
         $tableArr = [];

         //loop through the competition and get each table
         foreach($this->competitions as $competition){  

            //get datum
            $datum = $this->updateBringer(function($competition){

                $client = new \GuzzleHttp\Client();
                $response = $client->request('GET', 'https://api.football-data.org/v2/competitions/'.$competition->id.'/scorers', ['headers' => [
                    'X-Auth-token' => '30d8aa28ae5b4a60b541cf3ac0e5b818',
                    ]
                ]);
                return json_decode($response->getBody());
                
            }, $competition);

            
            foreach($datum->scorers as $data){

  
        
                $arr = [];
                
                $arr['team_id'] = $data->team->id;
                $arr['competition_id'] = $competition->id;
                $arr['numberOfGoals'] = $data->numberOfGoals;
                $arr['player_id'] = $data->player->id;
                $arr['player_name'] = $data->player->name;
                $arr['player_nationality'] = $data->player->nationality;
                $arr['player_position'] = $data->player->position;
                $arr['player_shirtNumber'] = $data->player->shirtNumber;
               
                array_push($tableArr, $arr); 
            }

            sleep(5);
        }
 
        //clear all data from the database table
        Scorer::truncate();
 
        //insert tables to database
        DB::table('scorers')->insert($tableArr);

        echo "all scorers updated\n";
      
    } 

    //get current time
    function TimeNow(){
        return strtotime(now());
    }


    //Main function that calls other functions
    function TodayUpdateDoer(){

        //filter competitions by id.
        $competitionFilter = function($arr){
            foreach($this->competitions as $competition){
                if($competition->id == $arr->competition->id){
                    return true;
                }
            }    
        };    
        
        //if their are no fixtures in database then fetch features.
        if(AllFixture::all()->isEmpty()){
            echo "initializing all fixtures...\n";
            $this->getCompetitionsFixtures();
        }

        //if their are no scorers in database then fetch the scorers
        if(Scorer::all()->isEmpty()){
            echo "initializing all scorers...\n";
            $this->getScorers();
        }

        //if their are no tables in database the fetch the tables
        if(Table::all()->isEmpty()){
            echo "initializing all scorers...\n";
            $this->getTables();
        }

        //if their are no teams in the database then fetch the teams
        if(Team::all()->isEmpty()){
            echo "initializing all Teams...\n";
            $this->getLeagueTeams();
        }
      
        //just used for testing: count update loops
        $counter = 0;

        //duration of match
        $matchOffsetTime = '+180 minutes';

        //get all matches for today.
        $matchesForToday = $this->getTodayMatches();

        //filter the matches by desired competitions.
        $matchesForToday = array_filter($matchesForToday, $competitionFilter);



        //if no matches exists
        if(count($matchesForToday) == 0){

            echo "No available Matches\n";

            //then get table
            $this->getTables();

            echo "sleeping for 10 sec\n";
            sleep(10);

            //then get scorers
            $this->getScorers();

            echo "sleeping for 10 sec\n";
            sleep(10);

            //then get league teams
            $this->getLeagueTeams();

            echo "sleeping for 10 sec\n";
            sleep(10);

            //get competition fixtures.
            $this->getCompetitionsFixtures();

            //stop execution of script and return.
            return;
        }

        //else if their are matches for the day...

        //get the last match for the day.
        $lastMatch = end($matchesForToday);

        //get time match ends for the day.
        $lastMatchTime = strtotime($matchOffsetTime, strtotime($lastMatch->utcDate));
       
        //////filter the matches;
        
        echo "available matches\n";

        //while the last match for the day has not been played..
        while($this->TimeNow() < $lastMatchTime){

            //start the long loop
            echo "sarting long loop\n";
            echo ++$counter."long loops passed\n";





            //loop through each match and check match time
            foreach($matchesForToday as $data){

                //match time + match offset time; time to complete a match.
                $timeUpperBoundary = strtotime($matchOffsetTime, strtotime($data->utcDate));

                //match time
                $timeLowerBoundary = strtotime($data->utcDate);

                //if it a match is going on
                if($this->TimeNow() > $timeLowerBoundary && $this->TimeNow() < $timeUpperBoundary){

                    

                    //check if match is live
                    $liveMatches = $this->checkForLiveMatches($matchesForToday);

                    
                    

                    //if their are live matches
                    while($liveMatches->isLiveMatchAvailable == true){

                        echo "live match loop\n";
                        
                        echo ++$counter." loop passed\n";

                        
                        ////

                        // echo ''.count($liveMatches). ' - ' .count($prevLiveMatches);

                        //debug output number of matches in progress
                        echo count($liveMatches->datum) ." matches in progress\n";

                        //a match has finished or is added

                        //get all matches for today.
                        $matchesForToday = $this->getTodayMatches();

                        //filter the matches by desired competitions.
                        $matchesForToday = array_filter($matchesForToday, $competitionFilter);

                        $prevLiveMatches = $this->checkForLiveMatches($matchesForToday);

                        foreach($matchesForToday as $dat){
            
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

                            DB::table('all_fixtures')->where('match_id', $dat->id)->update($arr); 
                            
                        }

                        echo "m_database updated\n";  
                        
                        









                        // //if it their still live matches
                        // if ($prevLiveMatches->isLiveMatchAvailable == true){
                            //if another live match has started or ended.
                            //possible fault: if match starts and end at the same time, then it wound reflect.
                            //hoping that does'nt happen :)
                            if($liveMatches->countMatches != $prevLiveMatches->countMatches){
                                //update tables and scorers
                                sleep(6);
                                echo "sleeping for 6 sec\n";
                                $this->getTables();
    
                                sleep(6);
                                echo "sleeping for 6 sec\n";
                                $this->getScorers();
                            }
                        // }

                        //update live matches.
                        $liveMatches->datum = $prevLiveMatches->datum;
                        $liveMatches->isLiveMatchAvailable = $prevLiveMatches->isLiveMatchAvailable;
                        $liveMatches->countMatches = $prevLiveMatches->countMatches;



                        

                        echo "sleeping for 8 sec\n"; 
                        sleep(8);
                        //end of loop for checking for live matches.
                    }
                }
            }//has checked for all available matches.
            sleep(8);

            //end of main loop.
        }

         //get table
         $this->getTables();

         //get league teams
         $this->getLeagueTeams();

         //get competition fixtures
         $this->getCompetitionsFixtures();

         //get competition scorers
         $this->getScorers();


    }  


    // function getMatche(Request $request){

    //     $this->getTables();
      
      
    //     $Even = function($array) 
    //     { 
    //         // returns if the input integer is even 
    //         if($array%2==0) 
    //         return TRUE; 
    //         else 
    //         return FALSE;  
    //     } ;

        
  
    //     $array = array(12, 0, 0, 18, 27, 0, 46); 
    //     print_r(array_filter($array, $Even)); 

    //     return;
      
    // }

    function tt(){
       
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://api.football-data.org/v2/competitions/2014/standings', ['headers' => [
                'X-Auth-token' => '30d8aa28ae5b4a60b541cf3ac0e5b818',
                ]
            ]);
            return json_decode($response->getBody());
            
       
    
    }

    function testo(){
            dd($this->getTables());
       
        }


        // $team = Team::all();
        // return new TeamResource(Team::find(1));

        // $this->getScorers();
        // $competitionFilter = function($arr){
        //     foreach($this->competitions as $competition){
        //         if($competition->id == $arr->competition->id){
        //             return true;
        //         }
        //     }    
        // }; 

        // $datum = $this->getTodayMatches();
        // $datum = array_filter($datum, $competitionFilter);
        // $datum = $this->checkForLiveMatches($datum);
        // return response()->json($datum, 200);
        // dd($datum);
        // return 'rob';

        // return AllFixtureResource::collection(AllFixture::where('competition_id', '2002')->get());
        // return new TableResource(Table::find(1));



        // $table = Table::find(5);
        // $team = Team::find(5);
        // $competition = Competition::find(2002);

        // dd($team->competition);


        // $this->getTables();       
    // }

}
