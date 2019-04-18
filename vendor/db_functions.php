<?php

    namespace vendor;

    use PDO;

    define("DB_SERVER","10.2.40.70");
    define("DB_USER","jnascimento");
    define("DB_PASS","Growin123*");
    define("DB_NAME","examefinal");
  
    //cria connecção à base de dados
    function db_connect(){
        try{
            $database = new PDO('mysql:host='.DB_SERVER.';dbname='.DB_NAME, DB_USER, DB_PASS);
            $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo "Con ok";
        }catch (PDOException $e){
            $msg ="Database connection failed: <br/>";
            $msg.= $e->getMessage();
            $msg.="(". $e->getCode().")";
            exit($msg);
        }
        return $database;
    }
    //fecha conecção a base de dados
    //em PDO não é necessario
    function db_disconnect($connection){
        //com o PDO para fechar a conexao 
        //basta por a variavel a NULL
        if(isset($connection)){
            $connection=null;
        }
    }
?>