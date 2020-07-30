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

class Fetcher extends Controller
{
    private $competitions;


    function __construct(){
        $this->competitions = Competition::all();
    }


    public function comparor($arr1, $arr2, $compareId, $ignoreArr = ["lastUpdated"]){

        $matched = false;
        if(count($arr2) != count($arr1)){
            echo("lengthNotMatched\n");
            return $matched;
        }
        
        foreach ($arr1 as $data){

            $matched = false;
            foreach ($arr2 as $data2){

                if($data[$compareId] == $data2[$compareId]){
                    $matched = true;
                    foreach ($data as $key => $value){

                        foreach ($ignoreArr as $toIgnore){
                            if($key == $toIgnore){
                                continue 2;
                            }
                        }

                        if($data[$key] == $data2[$key]){
                            // echo ("$value Matched \n");
                        }
                        else {
                            $matched = false;
                            echo ("$key Does not Match \n");
                            break 3;
                        }
                    }
                }
            }
            if($matched == false){
                echo("nomatcherFound\n");
                break;
            }
        }

        return $matched;
    }

    function getTables($prev_arr = [], $updateDatabase = true){

        echo('starting league tables update/n');
        //array for query mass insertion
        $tableArr = [];

        //loop through the competition and get each table
        foreach($this->competitions as $competition){
   
            $datum = UpdateBringer::updateBring(function($competition){

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

           
            $this->notifier($competition->name);

        }

        if($updateDatabase){   
            Table::truncate();
            DB::table('tables')->insert($tableArr);
            echo "tables has been updated\n";
       }

       $status = $this->comparor($prev_arr, $tableArr, 'team_id');
       return new Returner($tableArr, !$status);

    }

    function getCompetitionsFixtures($prev_arr = [], $updateDatabase = true){
      
        echo("starting league fixtures update\n");
        //array for query mass insertion
        $tableArr = [];

        //loop through the competition and get each table
        foreach($this->competitions as $competition){  

            $datum = UpdateBringer::updateBring(function($competition){
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

            $this->notifier($competition->name);
           
        }

        if($updateDatabase){   
    
        AllFixture::truncate();
        DB::table('all_fixtures')->insert($tableArr);
       }

       echo "all competitions fixtures updated\n";

       $status = $this->comparor($prev_arr, $tableArr, 'match_id');
       return new Returner($tableArr, !$status);
        
    }


    function getScorers($prev_arr = [], $updateDatabase = true){

        echo("starting league scorers update\n");
        
        //array for query mass insertion
        $tableArr = [];

        //loop through the competition and get each table
        foreach($this->competitions as $competition){  

           //get datum
           $datum = UpdateBringer::updateBring(function($competition){

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

           $this->notifier($competition->name);
       }


       if($updateDatabase){   
           
            Scorer::truncate();
            DB::table('scorers')->insert($tableArr);
            echo "Scorers database updated\n";
       }

       $status = $this->comparor($prev_arr, $tableArr, 'player_id');
       return new Returner($tableArr, !$status);
    }  
    
    function getLeagueTeams($prev_arr = [], $updateDatabase = true){

        echo "fetching league scorers\n";
        //get all competition
        //array for query mass insertion
        $tableArr = [];

        //loop through the competition and get each table
        foreach($this->competitions as $competition){
          
            echo("starting league Teams Update");
            //fetch teams from API
            $datum = UpdateBringer::updateBring(function($competition){
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

            $this->notifier($competition->name);
        }

        if($updateDatabase){   
            Team::truncate();
            DB::table('teams')->insert($tableArr);
            echo "Teams database updated\n";
       }

       $status = $this->comparor($prev_arr, $tableArr, 'id');
       return new Returner($tableArr, !$status);      
    }


    function notifier($competitionName){
        echo $competitionName. " has been queued\n\n";
        sleep(5);
    }

}

class Returner {

    public $arr;
    public $status;


    
    function __construct($arr, $status){
        $this->status = $status;
        
        $this->arr = $arr;
        
    }

    public function getArr(){
        return $this->arr;
    }

    public function hasArrayChanged(){
        return $this->status;
    }
}
