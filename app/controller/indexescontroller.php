<?php

namespace app\controller;

use app\Indexes;
use GuzzleHttp\Client;
use app\controller\LogController;
use app\controller\UserController;
/*
    Classe para controlar os indexes,esta classe vai chamar metodos que estão na classe do modelo Index, 
    chama a webApi e faz o render para as views (usando a Classe MyView),
    grava nos Logs o que é feito nos Indexes
*/
class IndexesController{

    /*
        Get:
            -> vai mostrar os indexes
            -> caso venha com $_POST passar para a função  getIndexesById com o id senão vai buscar os dados à BD e faz render
            -> verifica se há dados na BD, se houver apresenta senão vai fazer chamada ao método getWebApi que chama a Web api 
            e preenche a BD antes de fazer render 
             -> chama o controller de logs para gravar o pedido recebido
    */
    public static function get(){  
        if(count($_POST)==0){
            return json_encode("'Error'=>'Hash not valid'");
        } else{
            if(!UserController::is_logged_in($_POST['hash'])){
                return json_encode("'Error'=>'Hash not valid'");
            }
            $indexes = Indexes::find_all();
            if(count($indexes)==0){
                $indexeByWebApi = self::getWebApi();
                self::createIndexes($indexeByWebApi);
                $indexes = Indexes::find_all();
            }
            
            $argsToLog ['request'] = 'List Indexes';
            $argsToLog ['description'] = 'User listed the following indexes:'.json_encode($indexes);
            LogController::post($argsToLog);

            return json_encode($indexes);
        }
    }
    /*
        createIndexes:
            -> Preenche a BD com os dados retornado pela WebApi
            -> chama o controller de logs para gravar o pedido recebido
    */
    public static function createIndexes($args){
        
        foreach ($args as $object) { 
            $indexes= new Indexes();
            $index = json_decode($object);
            $indexes->symbol = $index->symbol;
            $indexes->description = $index->description;
            $indexes->spread_target_standard = $index->spread_target_standard;
            $indexes->trading_hours = $index->trading_hours;
            $indexes->type = $index->type;
            $indexes->save();
        }
        
        $argsToLog ['request'] = 'Create Indexes';
        $argsToLog ['description'] = 'User tried to list indexes but database has empty. Called web api to fill database.';
        LogController::post($argsToLog);
    } 

    /*
        getWebApi:
            -> Chama a WebApi e retorna os dados recebidos em JSON
    */
    public static function getWebApi(){
        $indices = 'https://www.xtb.com/api/uk/instruments/get?queryString=&branchName=uk&instrumentTypeSlug=indices&page=1&_=1550592039763';
        $client = new Client();
        $res = $client->request('GET', $indices);
        $result = json_decode((string)$res->getBody()); 
        $arrayToReturn=[];
        foreach ($result->instrumentsCollectionLimited->indices as $value) {
            $valueToReturn=[];
            $valueToReturn['symbol']=$value->symbol;
            $valueToReturn['description'] = $value->description;
            $valueToReturn['spread_target_standard'] =$value->spread_target_standard;
            $valueToReturn['trading_hours'] =$value->trading_hours;
            $valueToReturn['type'] =$value->type;
            array_push($arrayToReturn,json_encode($valueToReturn));
        }
        return $arrayToReturn;
    } 
  
    /*
        getIndexesByName:
            -> Chama a funcção find_by_id do model Indexes e faz render dos dados retornados pela BD
            -> chama o controller de logs para gravar o pedido recebido
    */
    public static function getIndexesByName(){
   
        if(!isset($_POST['hash'])){
            $error[]='Hash not valid';
            return json_encode($error);
        } else{
            if(!UserController::is_logged_in($_POST['hash'])){
                $error[]='Hash not valid';
                return json_encode($error);
            }
            $argsToLog ['request'] = 'Get Indexes By Name';
            $argsToLog ['description'] = 'Get information to index with Name: ' . $_GET['name'];
            LogController::post($argsToLog);
            $index= Indexes::find_by_name($_GET['name']);
            
            return  json_encode($index);
        }
    }
    
}

?>