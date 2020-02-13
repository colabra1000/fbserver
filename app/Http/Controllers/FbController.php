<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Competition;
use App\Table;
use App\AllFixture;
use App\Team;
use Football;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Console\Commands\UpdateAllFixtures;

class FbController extends Controller
{

    private $competitions;
    private $todayDate;
    private $returnError;
    private $returnErrorCount;
    

    function __construct(){
        //get all competition
        $this->competitions = Competition::all();
        $this->todayDate = date('Y-m-d', strtotime(now()));
        $this->returnError = false;
        $this->returnErrorCount = 0;
    }

    function updateBringer($returner, $arg = null){
         //loop through the competition and get each table
        
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
 
                     echo "sleepin 40\n";    
                     $clientError  = true;

                 }
 
                //if upto 5 retries, end
                if($returnErrorCount >= 5){
                    // goto:endpoint;
                    //update state.
                    die('robani');
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

            $datum = $this->updateBringer(function($competition){
                return Football::getLeagueStandings($competition->id);
            }, $competition);

            foreach($datum[0]->table as $data){
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

            echo "table updated\n";

            sleep(5);

        }

        //clear all data from the database table
        Table::truncate();

        //insert tables to database
        DB::table('tables')->insert($tableArr);

        echo "tables gotten\n";
       
   }

   function getCompetitionsFixtures(){

         //array for query mass insertion
         $tableArr = [];

         //loop through the competition and get each table
         foreach($this->competitions as $competition){  

            //get datum
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
          
            //get table from API
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
                $arr['lastUpldated'] = $data->lastUpdated;
                array_push($tableArr, $arr); 
            }

            sleep(5);
        }

        //clear all data from the database table
        Team::truncate();

        //insert tables to database
        DB::table('teams')->insert($tableArr);

        echo "Leagues Team updated\n";
    }

    private function checkForLiveMatches($datum){
        array_filter($datum, function(){
            if($data->status == "IN_PLAY" || $data->status == "PAUSED"){
                return true;
            }
        });

        if (count($datum) > 0){
            return $datum;
        }

        return false;
    }

   

    private function getTodayMatches(){

        $datum = $this->updateBringer(function(){
            return Football::getMatches(['competitions' => '', 'dateFrom' => $this->todayDate, 'dateTo' => $this->todayDate])->toArray();
        });
       
        return $datum;
    }

    function TodayUpdateDoer(){

        $competitionFilter = function($arr){
            foreach($this->competitions as $competition){
                if($competition->id == $arr->competition->id){
                    return true;
                }
            }    
        };     
      
        $timeNow = strtotime(now());

        $matchOffsetTime = '+180 minutes';

        $datum = $this->getTodayMatches();

        $datum = array_filter($datum, $competitionFilter);

        if(count($datum) == 0){

            echo "No available Matches\n";

            //get table
            $this->getTables();

            sleep(10);

            //get league teams
            $this->getLeagueTeams();

            sleep(10);
            //get competition fixtures
            $this->getCompetitionsFixtures();

            return;
        }

        $lastMatch = end($datum);

        $lastMatchTime = strtotime($matchOffsetTime, strtotime($lastMatch->utcDate));
       
        //filter the matches;
        
        echo "available matches\n";

        die;

        while($timeNow < $lastMatchTime){
            echo "long loop\n";

            //loop through and check match time
            foreach($datum as $data){

                //match time + match offset time
                $timeUpperBoundary = strtotime($matchOffsetTime, strtotime($data->utcDate));
                //match time

                $timeLowerBoundary = strtotime($data->utcDate);

                //if time of match
                if($timeNow > $timeLowerBoundary && $timeNow < $timeUpperBoundary){

                    echo "its match time\n";

                    $prevLiveMatches = [];

                    //check if any match is live
                    while($liveMatches = $this->checkForLiveMatches($datum)){
                        
                        //get all matches
                        $datum = $this->getTodayMatches();
                        
                        //filter matches
                        $datum = array_filter($datum, $competitionFilter);
                        echo "response gotten\n";           
                        
                        foreach($datum as $dat){
            
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

                        echo "database updated\n";                          

                        //a match has finished or is added
                        if(count($liveMatches) != count($prevLiveMatches)){
                            //update table and sleep

                            sleep(8);
                            $this->getTables();
                        }

                        //sleep 
                        sleep(8);
                    }
                }
            }
            sleep(8);
        }

         //get table
         $this->getTables();

         //get league teams
         $this->getLeagueTeams();

         //get competition fixtures
         $this->getCompetitionsFixtures();
    }  


    function getMatche(Request $request){

        $this->getTables();
      
      
        $Even = function($array) 
        { 
            // returns if the input integer is even 
            if($array%2==0) 
            return TRUE; 
            else 
            return FALSE;  
        } ;

        
  
        $array = array(12, 0, 0, 18, 27, 0, 46); 
        print_r(array_filter($array, $Even)); 

        return;
      
    }

    function testStuffs(){
        $this->getTables();       
    }

}

 //get first match time
        // $latestMatchTime = 0;
        // $firstMatchTime = INF;

        // foreach($dat as $d){
          
        //     if(strtotime($d->utcDate) > $latestMatchTime){
        //         $latestMatchTime = strtotime($d->utcDate);   
        //     }

        //     if(strtotime($d->utcDate) < $firstMatchTime){
        //         $firstMatchTime = strtotime($d->utcDate);   
        //     }
           
// };
