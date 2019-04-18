<?php
namespace app;

use vendor\DatabaseObject;
/*
    Classe Modelo de Indexes

    -> utiliza a classe DatabaseObject que tem os metodos dinamicos para chamar a base de dados.
*/
class EmailsToRegist extends DatabaseObject{

    //nome da tabela que vai ser usada quando chamar os metodos da base de dados
    protected static $table_name="emailsToRegist";

    //nome das colunas da tabela indexes
    protected static $db_columns=['id','email'];

    public $id;
    public $email;
    

    public function __construct($args=[]){
        $this->email = isset($args['email']) ? $args['email'] :'';
       
    }

     //vai procurar o utilizador pelo o email e se existir devolve os dados
     public static function find_by_email($email){
        $query = self::$database->prepare("SELECT * FROM ".static::$table_name." WHERE email = :email");
        $query->bindParam(':email',$email);
        
        $object_array= static::find_by_sql($query);
        if(!empty($object_array)){
            //retorna o primeiro elemento do inicio do array
            return true;
        }else{
            return false;
        }
    }
}

?>