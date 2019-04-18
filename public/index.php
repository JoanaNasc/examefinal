<?php 
require '../vendor/boot.php';





$path = isset($_SERVER['REDIRECT_QUERY_STRING']) ? ltrim($_SERVER['REDIRECT_QUERY_STRING'], '/') :'';

$elements = explode('/', $path);    

$controller = isset($_GET['ct']) ? $_GET['ct'] : '';  
$method = isset($_GET['method']) ? $_GET['method']:'';
$data ='';

$ip = $_SERVER['REMOTE_ADDR'];

$whiteList = new App\Controller\IpsWhiteListedController();
if(!$whiteList->get($ip)){
    $ipsBlocked = new App\Controller\IpsBlockedController();
    $res=$ipsBlocked->get($ip);
    if($res['counter']>0){
        $ipsBlocked->update($ip,$res['counter']);
        echo json_encode('Invalid Argument');
    }else{
        $ipsBlocked->save($ip);
        echo json_encode('Invalid Argument');
    }
    echo json_encode('Invalid Argument');
}

$csrf=create_csrf_token();
$_SESSION['csrf_token']=$csrf;

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
    echo json_encode('Invalid Argument');
}

?>
