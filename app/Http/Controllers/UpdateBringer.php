<?php

namespace App\Http\Controllers;

class UpdateBringer extends Controller{



    public static function updateBring($returner, $arg = null){
            //loop through the competition and get each table
            
                //set numbers to retry to 0
                $returnErrorCount = 0;
                do{
                    $clientError = false;
                    $returnErrorCount ++;
    
                    try{
                        
                        $datum = $returner($arg);
                        
                    }catch(\GuzzleHttp\Exception\ConnectException $e){

                    
                        echo "cant update0 \n";
                      
                        $clientError = true;
                    
                    }catch(\GuzzleHttp\Exception\ClientException $e){
    
                        echo "cant update1 \n"; 
                       
                        $clientError  = true;

                    }catch(\GuzzleHttp\Exception\RequestException $e){
                        echo($e."\n");
                        echo "this unknown curl error\n";    
                        $clientError  = true;
                    }

                    // echo "sleepin 6 sec\n"; 
                    sleep(6);
    
                    //if upto 5 retries, end
                    if($returnErrorCount >= 5){
                    
                        die('errors contact admnistrator');
                    }
    
                }while($clientError == true);

                return $datum;
        }
}