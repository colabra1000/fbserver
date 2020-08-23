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


    public function testor4($competition){

        $fetcher = new Fetcher();
        $fetcher->getScorers([], false);
        // $ra = $competition::where('id', 2002)->get();
        // echo $ra;
    }


    public function testEvent(){
        event(new TestEvent("Robson kat"));
    }
}

// DB::beginTransaction();
//      // do all your updates here
//         foreach ($users as $user) {
//             $new_value = rand(1,10) // use your own criteria
//             DB::table('users')
//                ->where('id', '=', $user->id)
//                ->update(['status' => $new_value  // update your field(s) here
//                 ]);
//         }
//     // when done commit
// DB::commit();


// public function regenerateDescendantsSlugs(Model $parent, $old_parent_slug)
//     {
//         $children = $parent->where('full_slug', 'like', "%/$old_parent_slug/%")->get();

//         \DB::transaction(function () use ($children, $parent, $old_parent_slug) {
//             /** @var Model $child */
//             foreach ($children as $child) {
//                 $new_full_slug  = $this->regenerateSlug($parent, $child);
//                 $new_full_title = $this->regenerateTitle($parent, $child);

//                 \DB::table($parent->getTable())
//                     ->where('full_slug', '=', $child->full_slug)
//                     ->update([
//                         'full_slug' => $new_full_slug,
//                         'full_title' => $new_full_title,
//                     ]);
//             }
//         });
//     }

       // DB::BeginTransaction();

                // foreach($tableArr as $arr){
                //     DB::table($modelName)
                //     ->where($comparor, '=', $comparee)
                //     ->update($arr);
                // }

                // DB::commit();
