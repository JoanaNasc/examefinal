<?php
namespace app;

use vendor\DatabaseObject;
/*
    Classe Modelo de Indexes

    -> utiliza a classe DatabaseObject que tem os metodos dinamicos para chamar a base de dados.
*/
class IpsWhiteListed extends DatabaseObject{

    //nome da tabela que vai ser usada quando chamar os metodos da base de dados
    protected static $table_name="ipsWhiteListed";

    //nome das colunas da tabela indexes
    protected static $db_columns=['id','ips'];

    public $id;
    public $ips;

    public function __construct($args=[]){
        $this->ips = isset($args['ips']) ? $args['ips'] :'';
    }
    public static function fin_by_ip($ip){
        $query = self::$database->prepare("SELECT * FROM ".static::$table_name." WHERE ips = :ip");
        $query->bindParam(':ip',$ip);
        $object_array= static::find_by_sql($query);
            if(!empty($object_array)){
                return true;
            }else{
                return false;
            }
    }
}

?>