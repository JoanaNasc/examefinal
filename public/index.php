<?php 
require '../vendor/boot.php';

$ip = $_SERVER['SERVER_ADDR'];

$csrf=create_csrf_token();
$_SESSION['csrf_token']=$csrf;


$path = isset($_SERVER['REDIRECT_QUERY_STRING']) ? ltrim($_SERVER['REDIRECT_QUERY_STRING'], '/') :'';

$elements = explode('/', $path);    

$controller = isset($_GET['ct']) ? $_GET['ct'] : '';  
$method = isset($_GET['method']) ? $_GET['method']:'';
$data ='';


//verifica se o pedido tras um $_POST 
if(isset($_POST) && count($_POST)!==0){
    $data=$_POST;
}else if(isset($_GET)&& count($_GET)!==0){
    $data=$_GET;
}
if(csrf_token_is_valid($csrf)){
    $controller = 'App\Controller\\' . $controller;
    $ct = new $controller;
    echo $ct->$method($data);
}else{
    return json_encode('Invalid Argument');
}

?>
