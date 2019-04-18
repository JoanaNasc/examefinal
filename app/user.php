<?php
    namespace app;

    use vendor\DatabaseObject;
    use vendor\Functions;
    /*
    Classe Modelo de User

    -> utiliza a classe DatabaseObject que tem os metodos dinamicos para chamar a base de dados.
    -> valida os dados recebidos antes de chamar as funçoes da base de dados
*/
    
    class User extends DatabaseObject{

        //nome da tabela que vai ser usada quando chamar os metodos da base de dados
        protected static $table_name='users';

        // nome das colunas que a tabela users tem
        protected static $db_columns =['id','username','hashed_password','last_login','email','hash_confirm','hash'];
        
        public $id;
        public $username;
        public $hashed_password;
        public $password;

        public $last_login;
        public $email;
        public $hash_confirm;

        public $hash;

        public function __construct($args=[]){
            $this->email = isset($args['email']) ? $args['email'] : '';
            $this->username = isset($args['username']) ? $args['username'] : '';
            $this->password = isset($args['password']) ? $args['password'] : '';
           
        }   

        //faz o hash da password com o metodo de encriptação BCrypt
        protected function set_hashed_password(){
            $this->hashed_password=password_hash($this->password,PASSWORD_BCRYPT);
        }

        //passa o email de binario para hexadecimal, este vai ser o token usado para confirmar o email
        protected function set_hashed_confirm(){
            $this->hash_confirm=bin2hex($this->email);//hash para confirmar o email
        }

        //verifica se a password recebida é igual a password do utilizador
        public function verify_password($password){
            return password_verify($password,$this->hashed_password);
        }

        //vai procurar o utilizador pelo o username e se existir devolve os dados
        public static function find_by_username($username){
            $query = self::$database->prepare("SELECT * FROM ".static::$table_name." WHERE username = :username");
            $query->bindParam(':username',$username);
            
            $object_array= static::find_by_sql($query);
            if(!empty($object_array)){
                //retorna o primeiro elemento do inicio do array
                return array_shift($object_array);
            }else{
                return false;
            }
        }
        //vai procurar o utilizador pelo o email e se existir devolve os dados
        public static function find_by_email($email){
            $query = self::$database->prepare("SELECT * FROM ".static::$table_name." WHERE email = :email");
            $query->bindParam(':email',$email);
            
            $object_array= static::find_by_sql($query);
            if(!empty($object_array)){
                //retorna o primeiro elemento do inicio do array
                return array_shift($object_array);
            }else{
                return false;
            }
        }

        //verifica se o utilizador está logado ou nao. Se existir $_SESSION['username'] está logado e retorna true, senao retorna false
        public static function is_logged_in($hash) :bool {
            $query = self::$database->prepare("SELECT * FROM ".static::$table_name." WHERE hash = :hash");
            $query->bindParam(':hash',$hash);
            $object_array= static::find_by_sql($query);
            if(!empty($object_array)){
                return true;
            }else{
                return false;
            }
        }

        /*
            chama as funçoes de encriptaçao do token e da password e depois chama a função da classe DatabaseObject
            para criar o utilizador
        */
        public function create(){
            $this->set_hashed_password();
            $this->set_hashed_confirm();
            return parent::create();
        }

        //confirma se o username já existe ou não
        public static function has_unique_username($username,$id="0") : bool{
            $user= self::find_by_username($username);
           if($user===false || $user->id ==$id){
                return true;
                }else{
                return false;
                }
        }
        //confirma se o email já existe ou não
        public static function has_unique_email($email,$id="0"): bool{
            $user= self::find_by_email($email);
            if($user===false || $user->id ==$id){
                 return true;
                 }else{
                 return false;
                 }
        }

        //confirma se aquele user existe na base de dados e se existir faaz update e passa o campo hash_confirm para confirmed
        public static function changeRandomPassword($email,$password,$newPassword){
            
            $newPassword =password_hash($newPassword,PASSWORD_BCRYPT);
            $query = self::$database->prepare("SELECT * FROM ".static::$table_name." WHERE email = :email and 
            (minute(last_login)-minute(DATE_SUB(NOW(), INTERVAL 5 MINUTE))) between 0 and 5");
            $query->bindParam(':email',$email);
            
            $emailfound= static::find_by_sql($query); 
            if($emailfound==false || $emailfound==0){
                //email nao foi encontrada na base de dados.
                return false;
            }else{
                $query = self::$database->prepare("UPDATE users SET hashed_password = :newpassword, hash_confirm= 'confirmed' WHERE email = :email limit 1;");
                $query->bindParam(':newpassword',$newPassword);
                $query->bindParam(':email',$email);
                $exec=$query->execute();
                if($exec){
                    return true;
                }else{
                    return false;
                }
            }
        }

        public static function update_last_login($id,$time,$hash){
            $query = self::$database->prepare("UPDATE users SET last_login = :time, hash= :hash WHERE id = :id LIMIT 1;");
            $query->bindParam(':id',$id);
            $query->bindParam(':time',$time);
            $query->bindParam(':hash',$hash);
            $exec=$query->execute();
            if($exec){
                return true;
            }else{
                return false;
            }
        }
        public static function deleteByEmail($email){
            $query="DELETE FROM users WHERE email=:email LIMIT 1";
            $sql=self::$database->prepare($query);
            $sql->bindParam(':email',$email);
            $exec=$sql->execute();
            if($exec){
                return true;
            }else{
                return false;
            }
        }
    }
?>