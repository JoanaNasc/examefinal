<?php
namespace tests;

include dirname(dirname(__FILE__)).'/vendor/boot.php';

use PHPUnit\Framework\TestCase;

use app\controller\IndexesController;
use app\controller\UserController;
use vendor\DatabaseObject;
use vendor;

class IndexesControllerTest extends TestCase{
    
    public $database;

    public function setUp():void{ 
        $this->user = new UserController();
        $args['username'] = 'JoanaNasc';
        $args['password'] = 'Jnascimento123*';
        $_SERVER["REMOTE_ADDR"] ='127.0.0.1';
        $result = json_decode($this->user->login($args),true);
        
        $this->user->hash = $result['login'];
        $this->index=new IndexesController();
        //$this->defineConstants();
    }

    public function tearDown():void{
        unset($this->index);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
    **/
    public function testGetIndexByNamePass(){
        $expected = [
            'id'=>'2',  
            'symbol'=>'AUT20', 
            'description'=>'Instrument which price is based on quotations of the contract for index reflecting 20 largest Austrian stocks quoted on the Austrian regulated market.', 
            'spread_target_standard'=>'28', 
            'trading_hours'=>'9:10 am  - 5:00 pm', 
            'type'=>'indices'
        ];
        $_GET['name'] = 'AUT20';
        $_POST['hash'] = $this->user->hash; 
        $return = $this->index->getIndexesByName();
        $output =json_decode($return, true);
       
       
        unset($output['errors']);

        ksort($expected);
        ksort($output);
 
        
        $this->assertEquals(
            $expected,
            $output
        );
    }
 

     /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
    **/
    public function testGetIndexByNameFail(){
        $_GET['name'] = 'BRAComp';
        $_POST['hash'] = $this->user->hash;
        $expected = [
            'id'=>'2',  
            'symbol'=>'AUT20', 
            'description'=>'Instrument which price is based on quotations of the contract for index reflecting 20 largest Austrian stocks quoted on the Austrian regulated market.', 
            'spread_target_standard'=>'28', 
            'trading_hours'=>'9:10 am  - 5:00 pm', 
            'type'=>'indices'
        ];

        $return = $this->index->getIndexesByName();
        
        $output =json_decode($return, true);
       
       
        unset($output['errors']);

        ksort($expected);
        ksort($output);
 
        
        $this->assertNotEquals(
            $expected,
            $output
        );
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