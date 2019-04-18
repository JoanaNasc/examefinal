<?php
namespace tests;

include dirname(dirname(__FILE__)).'/vendor/boot.php';

use PHPUnit\Framework\TestCase;

use app\controller\LogController;
use app\controller\UserController;
use vendor\DatabaseObject;
use PDOException;
use vendor;

class LoggerControllerTest extends TestCase{
    
    public $database;

    public function setUp():void{ 
        $this->user = new UserController();
        $args['username'] = 'JoanaNasc';
        $args['password'] = 'Jnascimento123*';
        $_SERVER["REMOTE_ADDR"] ='127.0.0.1';
        $result = json_decode($this->user->login($args),true);
        
        $this->user->hash = $result['login'];
        $this->log=new LogController();
        //$this->defineConstants();
    }

    public function tearDown():void{
        unset($this->log);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
    **/
    public function testPostPass(){
        $argsToLog ['username']='test PHPUnit';
        $argsToLog ['request'] = 'Test Post ';
        $argsToLog ['description'] = 'Test POST Sucess from ip:127.0.0.1';
        $output=$this->log->post($argsToLog);
        
        $this->assertTrue($output);
    }
 

     /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
    **/
    public function testPostFail(){

        $argsToLog ['username']=NULL;
        $argsToLog ['request'] =NULL;
        $argsToLog ['description'] = 'Test POST fail from ip:127.0.0.1';

        $this->expectException(PDOException::class);

        $output=$this->log->post($argsToLog);
        
        
    }
 
    








    /*public function defineConstants(){
        $this->database = vendor\db_connect();
        vendor\DatabaseObject::set_database($this->database);

        define("PROJECT_PATH", dirname(__DIR__). '/');
        define("APP_PATH", PROJECT_PATH .'app');
        define("CONTROLLER_PATH", APP_PATH . '/controller');
        define("PUBLIC_PATH", PROJECT_PATH .'public');
        define("RESOURCES_PATH", PROJECT_PATH .'resources');
        define("SAFE_FILES_PATH", PROJECT_PATH .'safeFiles');
        define("VENDOR_PATH", PROJECT_PATH .'vendor');
        $_SERVER["REMOTE_ADDR"] ='127.0.0.1';
        return;
    }*/
       

}
?>