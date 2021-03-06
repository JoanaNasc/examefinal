<?php
require '/var/www/examefinal.test/vendor/boot.php';
use GuzzleHttp\Client;

function login(){
   
    $url = "http://examefinal.test/api/auth/";
    $body = [
        'form_params' => [
            'username' => 'jcnasc.90@gmail.com',
            'password' => 'Jnascimento123*',
        ]
    ];
    
    $result = getApi($url,'POST',$body);
  
    $loginRet = json_decode($result,true);
   
    return $loginRet['login'];
}

function register(){
  
    $url = "http://examefinal.test/api/register";
    $body = [
        'form_params' => [
            'email' => 'jcnasc.90@gmail.com',
        ]
    ];
    $result = getApi($url,'POST',$body);
   
    print_r($result);
}
function alter(){
    $url = "http://examefinal.test/api/alter";
    $body = [
        'form_params' => [
            'email' => 'jcnasc.90@gmail.com',
            'password' => '#ZCPe6DHfwdS5*',
            'newPassword'=> 'Jnascimento123*'
        ]
    ];
    $result = getApi($url,'POST',$body);
   
    print_r($result);
}
function getAllIndexes($hash=NULL){
    $url = "http://examefinal.test/api/indexes";
    $body = [
        'form_params' => [
            'hash' => $hash
        ]
    ];
    $result = getApi($url,'POST',$body);

  
    print_r($result);
}
function getIndexByName($name,$hash=NULL){
    $url = "http://examefinal.test/api/indexes/".$name;

    $body = [
        'form_params' => [
            'hash' => $hash
        ]
    ];
    $result = getApi($url,'POST',$body);

   
    print_r($result);
}

function getAllLogs($hash=NULL){
    $url = "http://examefinal.test/api/logs";
    $body = [
        'form_params' => [
            'hash' => $hash
        ]
    ];
    $result = getApi($url,'POST',$body);
    print_r($result);
}
function getAllLogsCSV($hash=NULL){
    $url = "http://examefinal.test/api/logs/getCsv";
    $body = [
        'form_params' => [
            'hash' => $hash
        ]
    ];
    $result = getApi($url,'POST',$body);
    print_r($result);
}

function saveCSVMoreThan7k($hash=NULL){
    $url = "http://examefinal.test/api/logs/saveCsv";
    $file = file_get_contents('/var/www/examefinal.test/resources/filesToUpload/cursophp_joana_more_than_7k.csv','r');
    
    $body = [
        'form_params' => [
            'hash' => $hash,
            'path' => $file
        ]
    ];

    $result = getApi($url,'POST',$body);
    print_r($result);
}

function saveCSVLessThan7k($hash=NULL){
    $url = "http://examefinal.test/api/logs/saveCsv";
    // Provide an fopen resource.
    $file = file_get_contents('/var/www/examefinal.test/resources/filesToUpload/cursophp_joana_less_than_7k.csv','r');
   
    $body = [
        'body' => [
            'hash' => $hash,
            'file' => $file
        ]
    ];
    $result = getApi($url,'POST',$body);
    print_r($result);
}


function getApi($url,$method,$data=null){
 
    $client = new Client();
   
    if(isset($data)){
        $res = $client->request($method, $url,$data);
    }else{
        $res = $client->request($method, $url);
    }
    $result =(string)$res->getBody(); 
    return $result;
} 

register();
//alter();

$hash=login();


//getAllIndexes($hash);
//getIndexByName('AUS200',$hash);
//getAllLogs($hash);
//getAllLogsCSV($hash);
//saveCSVMoreThan7k($hash);
//saveCSVLessThan7k($hash);
?>