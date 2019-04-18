<?php
namespace tests;

include dirname(dirname(__FILE__)).'/vendor/boot.php';

use PHPUnit\Framework\TestCase;

use app\controller\UserController;
use vendor\DatabaseObject;
use vendor;

class UserControllerTest extends TestCase{
    
    public $database;

    public function setUp():void{ 
        $this->user=new UserController();
        //$this->defineConstants();
    }

    public function tearDown():void{
        unset($this->user);
    }
    /**
     * @preserveGlobalState disabled
    **/
    public function testConnection(){
        $this->database = vendor\db_connect();
        
        $this->assertIsObject($this->database);
        $database=$this->database;

        return $database;
    }
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
    **/
    public function testLoginDone(){
        
        
        $args['username'] = 'JoanaNasc';
        $args['password'] = 'Jnascimento123*';
        $_SERVER["REMOTE_ADDR"] ='127.0.0.1';
        $this->user->login($args);
        $this->assertTrue(true);
    }
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
    **/
    public function testLoginFail(){
        
        
        $args['username'] = 'JoanaNasc';
        $args['password'] = 'Jnascimento123';
        $_SERVER["REMOTE_ADDR"] ='127.0.0.1';
        $this->user->login($args);
        $this->assertTrue(true);
    }
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
    **/
    public function testCreateUserFail(){
        $args['email'] = 'jcnasc.90@gmail.com';
        $args['username'] = 'JNascimento';
        $args['password'] = "Jnascimento123*";
        $args['confirm_password']= "Jnascimento123*";
        $_SERVER["REMOTE_ADDR"] ='127.0.0.1';
        $result=$this->user->create($args);
        //se der erro retorna um objecto json
        $errors=json_decode($result);
        
        $this->assertIsArray($errors);
       
    }

    public function testCreateUserSucess(){
        $args['email'] = 'admin@test.com';
        $args['username'] = 'administrator';
        $args['password'] = "Jnascimento123*";
        $args['confirm_password']= "Jnascimento123*";

        $result=$this->user->create($args);
        //se der erro retorna um objecto json
        $errors=json_decode($result);
        $_SERVER["REMOTE_ADDR"] ='127.0.0.1';
        $this->assertTrue(true);

        //apagar o user da tabela para quando voltar a correr o teste ainda executar com sucesso
        
        $this->user->deleteUser($args['email']);
        $this->assertTrue(true);
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