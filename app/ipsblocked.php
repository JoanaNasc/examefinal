<?php
namespace app;

use vendor\DatabaseObject;
/*
    Classe Modelo de Indexes

    -> utiliza a classe DatabaseObject que tem os metodos dinamicos para chamar a base de dados.
*/
class IpsBlocked extends DatabaseObject{

    //nome da tabela que vai ser usada quando chamar os metodos da base de dados
    protected static $table_name="ipsBlocked";

    //nome das colunas da tabela indexes
    protected static $db_columns=['id','ips','blocked','counter'];

    public $id;
    public $ips;
    public $blocked;
    public $counter;
    public function __construct($args=[]){
        $this->ips = isset($args['ips']) ? $args['ips'] :'';
        $this->blocked = isset($args['blocked']) ? $args['blocked'] :'';
        $this->counter = isset($args['counter']) ? $args['counter'] :'';
    }

    public static function fin_by_ip($ip){
        $query = self::$database->prepare("SELECT * FROM ".static::$table_name." WHERE ips = :ip");
        $query->bindParam(':ip',$ip);
        $object_array= static::find_by_sql($query);
        if(!empty($object_array)){
            return $object_array;
        }else{
            return false;
        }
    }
}

?>