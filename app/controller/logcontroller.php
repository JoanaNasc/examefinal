<?php
namespace app\controller;

use app\Log;
use vendor\Foundationphp\Exporter\Csv; 
use vendor\Foundationphp\Psr4Autoloader;
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
        $log->timestamp = date('Y-m-d H:i:s');

        $result=$log->save();
    }
    /*
        get:
            -> Chama a função find_all do modelo Log e faz render de todos os Logs recebidos da base de dados
    */

    public static function get(){
        $logs = Log::find_all();
        return json_encode($logs);
    }

    public static function getCsv(){
        $log = new Log();
        $results = $log->find_data_to_export();

        $loader = new Psr4Autoloader();
        $loader->register();
        $loader->addNamespace("Foundationphp","Foundationphp");
        try{
            $options['delimiter']="\t";
            
            new Csv($results,'logs.csv',$options);
        }catch(Exception $e){
            $error=$e->getMessage();
        }
    }

    public static function saveCsv(){
        
    }

}


?>