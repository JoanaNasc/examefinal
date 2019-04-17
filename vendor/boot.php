<?php
    ob_start();
    session_start();
    //Definição de variaveis para as pastas do projecto
    define("PROJECT_PATH", dirname(__DIR__). '/');
    define("APP_PATH", PROJECT_PATH .'app');
    define("CONTROLLER_PATH", APP_PATH . '/controller');
    define("PUBLIC_PATH", PROJECT_PATH .'public');
    define("RESOURCES_PATH", PROJECT_PATH .'resources');
    define("SAFE_FILES_PATH", PROJECT_PATH .'safeFiles');
    define("VENDOR_PATH", PROJECT_PATH .'vendor');

    require PROJECT_PATH.'vendor/autoload.php';
    include PROJECT_PATH.'vendor/db_functions.php';
   
   
    function my_autoload($className)
    {
        $filename = PROJECT_PATH . str_replace("\\", '/', $className) . ".php";
        $filename = strtolower($filename);
        if (file_exists($filename)) {
            
            include($filename);
            if (class_exists($className)) {
                return TRUE;
            }
        }
        return FALSE;
    }
    spl_autoload_register('my_autoload');
   
    
    $database = vendor\db_connect();
    vendor\DatabaseObject::set_database($database);        


?>