<?php
namespace app\controller;

use app\Log;
use app\controller\UserController;
use vendor\Functions; 
/*
    Classe que vai gravar os Logs na base de dados
*/
class LogController{
    /*
        post:
            -> Grava os dados recebidos por parametro na BD
    */
    public static function post($args){ 
        $log = new Log();
        $username = isset($args['username']) ? $args['username'] :'';
        $log->username= isset($_SESSION['username']) ? $_SESSION['username'] : $username;
        $log->operation = $args['request'];
        $log->description = $args['description'];
        $log->ip = $args['ip'];
        $log->timestamp = date('Y-m-d H:i:s');

        $result=$log->save();
        return $result;
    }
    /*
        get:
            -> Chama a função find_all do modelo Log e faz render de todos os Logs recebidos da base de dados
    */

    public static function get(){ 
        
        if(count($_POST)==0){
            return json_encode("'Error'=>'Hash not valid'");
        } else{
            if(!UserController::is_logged_in($_POST['hash'])){
                return json_encode("'Error'=>'Hash not valid'");
            }
            $logs = Log::find_all();
            return json_encode($logs);
        }
    }

    public static function getCsv(){
       
        if(count($_POST)==0){
            return json_encode("'Error'=>'Hash not valid'");
        } else{
            if(!UserController::is_logged_in($_POST['hash'])){
                return json_encode("'Error'=>'Hash not valid'");
            }
            $log = new Log();
            $results =(array)$log->find_all(); 
            
            //Give our CSV file a name.
            $csvFileName = RESOURCES_PATH.'/log.csv';

            //Open file pointer.
            $fp = fopen($csvFileName, 'w');
            $firstLine = (array) $results[0];
            array_pop($firstLine);
            $headers = array_keys($firstLine);print_r($headers);
            fputcsv($fp,$headers);
            
            $firstLineValues =array_values($firstLine);
            fputcsv($fp, $firstLineValues); 
            $result = (array)$results;
            //Loop through the associative array.
            for($i=1;$i < count($result); $i++){ 
                $values = (array)$result[$i];
                array_pop($values);
                $r=array_values($values);
                //Write the row to the CSV file.
                fputcsv($fp, $r);
            }
            
            //Finally, close the file pointer.
            fclose($fp);
        }
    }

    public static function saveCsv(){ 

        if(count($_POST)==0){
            return json_encode("'Error'=>'Hash not valid'");
        } else{
            if(!UserController::is_logged_in($_POST['hash'])){
                return json_encode("'Error'=>'Hash not valid'");
            }
            $funtion = new Functions();
            return $funtion->upload_file($_POST['path']);
        }
    }
}


?>