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
            'password' => 'cDuYv2PI$CgJ10*',
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
    $url = "http://examefinal.test/api/indexes/".$name."/".$hash;

    $result = getApi($url,'GET');
   
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

$hash=login();

//register();
//alter();
//getAllIndexes($hash);
//getIndexByName('AUS200',$hash);


?>