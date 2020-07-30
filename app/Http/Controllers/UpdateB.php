<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Fetcher;
use App\Events\TestEvent;

class UpdateB extends Controller
{

    public function testor(){

        
        $tableArr = [];

        for($i = 0; $i < 3; $i++){
        
            $arr = [];
            $arr['match_id'] = "id$i";
            $arr['match_day'] = "matchday$i";
            $arr['competition_id'] = "competition$i";
            $arr['status'] = "status$i";
            $arr['utcDate'] = "utcDate$i";
            $arr['lastUpdated'] = "lastUpdated$i";
            $arr['homeScore'] = "homeTeam$i";
            $arr['awayScore'] = "fullTime$i";
            $arr['homeTeam_id'] = "homeTeam$i";
            $arr['awayTeam_id'] = "awayTeam$i";
            array_push($tableArr, $arr); 
        }

        $tableArr2 = [];

        for($i = 0; $i < 3; $i++){
        
            $arr = [];
            $arr['match_id'] = "id$i";
            $arr['match_day'] = "matchday$i";
            $arr['competition_id'] = "competition$i";
            $arr['status'] = "status$i";
            $arr['utcDate'] = "utcDate$i";
            $arr['lastUpdated'] = "lastUpdated$i";
            $arr['homeScore'] = "homeTeam$i";
            $arr['awayScore'] = "fullTime$i";
            $arr['homeTeam_id'] = "homeTeam$i";
            $arr['awayTeam_id'] = "awayTeam$i";
            array_push($tableArr2, $arr); 
        }

        

       
        // $tableArr[4]['awayScore'] = "sesdfscserwsd";
        $tableArr2[0]['status'] = "sesdfscserwsd";

        $ff = new Fetcher();

        $r = $ff->comparor($tableArr2, $tableArr, "match_id");


        echo json_encode($r);     

    }

    public function testor3(){
        $fetcher = new Fetcher();


        $tar = [];

        for($i =0; $i < 4; $i++){

            if($i == 2){
                $tar[0]["player_name"] = "sfse";
            }

            $fee = $fetcher->getScorers($tar);

           
            // echo "rwby ". json_encode($fee->getArr()). "/n";
            $tar = $fee->getArr();
            echo " raa ". json_encode($fee->hasArrayChanged()). " $i \n";
        }
       

    }

    public function testor2(){


        $datum = [];
        $tableArr = [];
        for($i = 0; $i < 4; $i++){
            $data = new \StdClass();
            $data->id = "id $i";
            $data->competitionId = "competition $i";
            $data->numberOfGoals = "nog $i";
            $data->playerId = "playerId $i";
            $data->playerName = "playerName $i";

            array_push($data, $datum);

        }

        foreach($datum->scorers as $data){

 
       
            $arr = [];
            
            $arr['team_id'] = $data->id;
            $arr['competition_id'] = $data->competitionId;
            $arr['numberOfGoals'] = $data->numberOfGoals;
            $arr['player_id'] = $data->playerId;
            $arr['player_name'] = $data->playerName;
            
           
            array_push($tableArr, $arr); 
        }

        $fetcher = new Fetcher();

        for($i = 0; $i < 5; $i++){

        }



    }

    public function testEvent(){
        event(new TestEvent("Robson kat"));
    }
}
