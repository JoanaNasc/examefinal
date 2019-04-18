<?php
namespace app\controller;

use app\ipsblocked;
use app\controller\UserController;
use app\controller\LogController;
use vendor\Functions; 
/*
    Classe que vai gravar os Logs na base de dados
*/
class IpsBlockedController{
    /*
        get:
            -> vai verificar se o ip está white listed
    */
    public static function get($ip){ 
       $ips = new IpsBlocked();
       $result = $ips->fin_by_ip($ip);
       $res=(array)$result[0];
       
       return $res;
    }

    public static function save($ip){
        $ips = new IpsBlocked();
        $ips->ips=$ip;
        $result=$ips->create();
        if($result){
            return true;
        }else{
            return false;
        }
    }
    
    public static function update($ip,$count){
        $ips = new IpsBlocked();
        $res = $ips->update_count($ip,$count);
        
        return $res;
    }
}


?>