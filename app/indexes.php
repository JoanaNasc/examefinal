<?php
namespace app;

use vendor\DatabaseObject;
/*
    Classe Modelo de Indexes

    -> utiliza a classe DatabaseObject que tem os metodos dinamicos para chamar a base de dados.
*/
class Indexes extends DatabaseObject{

    //nome da tabela que vai ser usada quando chamar os metodos da base de dados
    protected static $table_name="indexes";

    //nome das colunas da tabela indexes
    protected static $db_columns=['id','symbol','description','spread_target_standard','trading_hours','type'];

    public $id;
    public $symbol;
    public $description;
    public $spread_target_standard;
    public $trading_hours;
    public $type;

    public function __construct($args=[]){
        $this->symbol = isset($args['symbol']) ? $args['symbol'] :'';
        $this->description = isset($args['description']) ? $args['description'] :'';
        $this->spread_target_standard = isset($args['spread_target_standard']) ? $args['spread_target_standard'] :'';~
        $this->trading_hours = isset($args['trading_hours']) ? $args['trading_hours'] :'';
        $this->type = isset($args['type']) ? $args['type'] :'';
    }

    //vai procurar o index pelo o nome/simbolo e se existir devolve os dados
    public static function find_by_name($symbol){
        $query = self::$database->prepare("SELECT * FROM ".static::$table_name." WHERE symbol = :symbol");
        $query->bindParam(':symbol',$symbol);
        
        $object_array= static::find_by_sql($query);
        if(!empty($object_array)){
            //retorna o primeiro elemento do inicio do array
            return array_shift($object_array);
        }else{
            return false;
        }
    }
}

?>