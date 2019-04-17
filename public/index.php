<?php 
require '../vendor/boot.php';

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

if(empty($elements[0])) {   
   app\controller\UserController::displayWelcome();
}else {
    $controller = 'App\Controller\\' . $controller;
    $ct = new $controller;
    echo $ct->$method($data);
}

?>
