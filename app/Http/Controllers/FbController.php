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

class FbController extends Controller
{

    private $competitions;
    private $fetcher;
    

    function __construct(){

        $this->fetcher = new Fetcher();
      
        //get saved competition from model
        $this->competitions = Competition::all();

    }

    function getTodayDate(){
        return date('Y-m-d', strtotime(now()));
    }

    //check for live matches
    // private function checkForLiveMatches($datum){
       
    //     $datum = array_filter($datum, function($data){
    //         if($data->status == "IN_PLAY" || $data->status == "PAUSED" ){
    //             return true;
    //         }
    //     });

        // return false;

        //create a class to return;
        // $obj = new \stdClass();
        
        //if their are any..
        // if (count($datum) > 0){

        //     return $datum;

            // $obj->isLiveMatchAvailable = true;
            // $obj->datum = $datum;
            // $obj->countMatches = count($datum);

            // return $obj;             
            
        // }

        // return null;

        //else
        
    //         $obj->isLiveMatchAvailable = false;
    //         $obj->datum = null;
    //         $obj->countMatches = 0;

    //         return $obj;    
        
    // }





    private function checkForLiveMatches($datum){
       

        foreach($datum as $data){
            if($data->status == "IN_PLAY" || $data->status == "PAUSED" ){
                return true;
            }
        }

        return false;

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
    function TimeNow(){
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
         $matchesForToday = $this->getTodayMatches();

         //filter the matches by desired competitions.
         return array_filter($matchesForToday, $competitionFilter);
    }


    //Main function that calls other functions
    function TodayUpdateDoer(){

        //filter competitions by id.
        // $competitionFilter = function($arr){
        //     foreach($this->competitions as $competition){
        //         if($competition->id == $arr->competition->id){
        //             return true;
        //         }
        //     }    
        // };  
        

        //to be put in a job;
       
        
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

        $matchesForToday = $this->getAndFilterMatchesForToday();

        //if no matches exists
        if(count($matchesForToday) == 0){

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

                    
                    $matchesForTodayArr_prev = [];

                    

                    //if their are live matches
                    // while($liveMatches->isLiveMatchAvailable == true){

                        $tableArr_prev = [];
                        $scorersArr_prev = [];
                    
                        while($liveMatches == true){

                    
                        echo "live match loop\n";
                        
                        echo ++$counter." loop passed\n";

                        //a match has finished or is added

                        $matchesForToday = $this->getAndFilterMatchesForToday();

                        // $prevLiveMatches = $this->checkForLiveMatches($matchesForToday);
                        $matchesForTodayArr = [];
                        
                        
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
                                
                                sleep(6);
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
                                

                                sleep(6);
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


                        //if another live match has started or ended.
                            //possible fault: if match starts and end at the same time, then it wouldnt reflect.
                            //hoping that does'nt happen :)
                            // if($this->fetcher->comparor($liveMatches, $prevLiveMatches)){
                            // if($liveMatches->countMatches != $prevLiveMatches->countMatches){
                            //     //update tables and scorers
                            //     sleep(6);
                            //     echo "sleeping for 6 sec\n";
                            //     $this->fetcher->getTables();
    
                            //     sleep(6);
                            //     echo "sleeping for 6 sec\n";
                            //     $this->fetcher->getScorers();
                            // }
                        // }


                        // $liveMatches = $prevLiveMatches;
                        //update live matches.
                        // $liveMatches->datum = $prevLiveMatches->datum;
                        // $liveMatches->isLiveMatchAvailable = $prevLiveMatches->isLiveMatchAvailable;
                        // $liveMatches->countMatches = $prevLiveMatches->countMatches;

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
       
        $updateB = new UpdateB();
        $updateB->testEvent();
    
    }


}
