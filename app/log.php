<?php
namespace app;
use vendor\DatabaseObject;

/*
    Classe Modelo de Log

    -> utiliza a classe DatabaseObject que tem os metodos dinamicos para chamar a base de dados.
*/

class Log extends DatabaseObject {
    
    //nome da tabela que vai ser usada quando chamar os metodos da base de dados
    protected static $table_name='logs';

    //nome das colunas da tabela logs
    protected static $db_columns=['id','username','operation','timestamp','description','ip'];    
    
    public $id;
    public $username;
    public $operation;
    public $timestamp;
    public $description;
    public $ip;

    public function __construct($args=[]){
        $this->username = isset($args['username']) ? $args['username'] : '';
        $this->operation = isset($args['operation']) ? $args['operation'] : '';
        $this->timestamp = isset($args['timestamp']) ? $args['timestamp'] : '';
        $this->description = isset($args['description']) ? $args['description'] : '';
        $this->ip = isset($args['ip']) ? $args['ip'] : '';
    }

    //uma vez que os logs tem de estar ordenados pela data de forma
    //descendente criei uma funçao especifica para nao alterar a que já está 
    // a ser usada por outros modelos e depois chama a funcçao find_by_sql da classe 'pai' DatabaseObject 
    public static function find_all(){
        $query = self::$database->prepare("SELECT * FROM ".static::$table_name." ORDER BY timestamp DESC;");
        return parent::find_by_sql($query);
    }

    public static function find_data_to_export(){
        $query = self::$database->prepare("SELECT * FROM ".static::$table_name." ORDER BY timestamp DESC;");
        
        try{
            $query->execute();
            
            $errorInfo = $query->errorInfo();
            
            if(!$errorInfo){
                exit("Database query failed.");
            }
            return $query;
        }catch(PDOException $e){
            die("Error on Database.".'<br/>'.$e->getMessage().'<br/><br/>'.$query->debugDumpParams());  
        }

    }
    
}

?>