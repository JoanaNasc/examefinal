<?php
namespace app\controller;

use app\ipswhitelisted;
use app\controller\UserController;
use app\controller\LogController;
use vendor\Functions; 
/*
    Classe que vai gravar os Logs na base de dados
*/
class IpsWhiteListedController{
    /*
        get:
            -> vai verificar se o ip está white listed
    */
    public static function get($ip){ 
       $ips = new IpsWhiteListed();
       $res = $ips->fin_by_ip($ip);
       return $res;
    }
    

}


?>