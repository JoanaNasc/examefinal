<?php
namespace app\controller;

use app\user;
use vendor\MyView;
use vendor\Functions;
use app\controller\LogController;
use app\controller\SwiftMailerController;
/*
    Classe para executar os metodos de user:
        -> login
        -> logout
        -> new
        -> create
        -> is_logged_in
        -> confirmemail
*/
class UserController{
    /*  
        login:
            -> verifica se o username e a password estão em branco. Se estiver devolve um erro
            -> Chama as funcçoes de find_by_username e verify_password do modelo User para confirmar se existe e se pode ser logado
            -> Se estiver tudo bem grava o username na variavel global $_SESSION
            -> senao faz render e envia os erros de volta
    */
    public static function login($args){ 
        $errors=[]; 
         
       
        $username=isset($args['username'])?$args['username'] :'';
        $password=isset($args['password'])?$args['password']:'';

        if(Functions::isblank($username)){
           $errors[]= 'Username cannot be blank!';
        }
        if(Functions::isblank($password)){
            $errors[] .= 'Password cannot be blank!';
        }
        
        if(count($errors)==0){
            $userExists= User::find_by_username($username);
            $passwordIsCorrect = $userExists->verify_password($password);
            if($userExists && $passwordIsCorrect && strcmp($userExists->hash_confirm,'confirmed')==0){
                $time=date('Y-m-d H:i:s');
                
                $toReturn['login'] =hash('sha256',$userExists->hashed_password);
                User::update_last_login($userExists->id,$time,$toReturn['login']);

                $argsToLog ['request'] = 'Login';
                $argsToLog ['description'] = 'Login from ip:'.$_SERVER['REMOTE_ADDR'];
                LogController::post($argsToLog);
              
                return json_encode($toReturn);
            }else{
                $errors[] = 'Username or Password wrong!';
                return json_encode($errors);
            }
        }else{
            $myview->errors=$errors;
            return json_encode($errors);
        }
    }

    /*
        create:
            -> cria um novo utilizador
            -> valida com a funçao validate do modelo User se os campos estão todos correctos 
            -> chama o metodo create do modelo User para criar o utilizador, 
                se der erro devolve o erro e faz render do mesmo formulario senão manda email e mensagem para o slack, e faz render para 
                a pagina de login
            -> chama o LogController para guardar os logs
    */
    public static function create($args){
        $user = new User();
        
        $user->email = $args['email'];
        $user->username = $args['email'];
        
        $user->password = self::generatePassword(12);
        $user->last_login = date('Y-m-d H:i:s');

        
        $errors = self::validate($user);

        if(!empty($errors)){
            return json_encode($errors);
        }else{            
            $created=$user->create();
            if($created){
                $argsToLog ['username'] = $user->username;
                $argsToLog ['request'] = 'Create User';
                $argsToLog ['description'] = 'User Create from ip:'.$_SERVER['REMOTE_ADDR'];
                LogController::post($argsToLog);
                //enviar e-mail
                $token = bin2hex($user->email);//token gerado a partir do email   
                $messageToSlack = "Your information is:
                    username: $user->username
                    password:$user->password
                    <h1>PLEASE NOTE: You only have 5 minutes to confirm!</h1><br/>
                    <h6>If you have not registered, please ignore this email.</h6>";

                $message = <<<EOT
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>Confirm Email Test</title>
</head>
<body link="#B64926" vlink="#FFB03B">
Your information is:
    username: $user->username
    password:$user->password
<h1>PLEASE NOTE: You only have 5 minutes to confirm!</h1><br/>
<h6>If you have not registered, please ignore this email.</h6>
</body>
</html>
EOT;
                $title ="Alter Password";
                $email=SwiftMailerController::sendEmail($user->email,$message,$title);
                if($email){
                    $argsToLog ['username'] = $user->username;
                    $argsToLog ['request'] = 'Send Email';
                    $argsToLog ['description'] = 'Email sent to: '.$user->email;
                    LogController::post($argsToLog);
                    Functions::messageToSlack(strip_tags($message));
                    return true;
                }else{
                    $argsToLog ['username'] = $user->username;
                    $argsToLog ['request'] = 'Send Email';
                    $argsToLog ['description'] = 'Error sending email to: '.$user->email;
                    LogController::post($argsToLog);
                    $errors[] =  $argsToLog ['description'];
                    return json_encode($errors);
                }
            }else{
                $argsToLog ['username'] = $user->username;
                $argsToLog ['request'] = 'Send Email';
                $argsToLog ['description'] = 'Error sending email to: '.$user->email;
                LogController::post($argsToLog);
                $errors[]="Error creating user.Please try again.";
                return json_encode($errors);
            }
            
        }  
    }

    /*
        logout:
            -> chama o LogController para guardar os logs
            -> faz unset da Session['username']
            -> reenvia para o homepage
    */
    public static function logout(){ 
        LogController::post(['request'=>'Logout','description'=>'Logout from ip:'.$_SERVER['REMOTE_ADDR']]);
        unset($_SESSION['username']);
        header('Location:/');
        exit;
    }

    /*
        is_logged_in:
            -> verifica se estamos logados chamado o modelo de User
    */
    public static function is_logged_in($hash): bool {
        return User::is_logged_in($hash);
    }
    /*
        list:
            -> chama a funçao find_all do modelo de User para mostrar todos os utilizadores
            -> faz render do que recebe para user_list
    */
    public static function list(){
        LogController::post(['request'=>'List','description'=>'List all users']);
        $users= User::find_all();return $users;
        return json_encode($users);
    }

    /*
        confirmemail:
            -> recebe o dados enviados por email/slack e chama a funçao changeRandomPassword do modelo para validar
    */

    public static function alter($args){
  
        $errors=[];
        $email=$args['email'];
        $password=$args['password'];

        $confirmedHash=User::changeRandomPassword($email,$password,$args['newPassword']);
        
        if($confirmedHash!==0 && $confirmedHash){
            LogController::post(['request'=>'Password changed','description'=>'Email confirmed from ip:'.$_SERVER['REMOTE_ADDR']]);
            $message='Password Changed';
            return json_encode($message);
        }else{
            $message['message']="We were unable to verify your registration. Please try again.";
            LogController::post(['request'=>'Confirm Email','description'=>$message['message']]);
            $errors[]=$message;
            return json_encode($errors);
        }
    }

    //faz as validações precisas para ver se podemos criar um novo registo.
    public static function validate($user) {
        $errors = [];
      
        if(Functions::isblank($user->email)) {
            $errors[] = "Email cannot be blank.";
        } elseif (!Functions::has_length($user->email, array('max' => 255))) {
            $errors[] = "Last name must be less than 255 characters.";
        } elseif (!Functions::validateEmail($user->email)) {
            $errors[] = "Email must be a valid format.";
        }else if( !$user->has_unique_email($user->email,$user->id ?? 0)){
            $errors[] = "Email not allowed. Try another!";
        }
        
        if(Functions::isblank($user->username)) {
          $errors[] = "Username cannot be blank.";
        } else if (!Functions::has_length($user->username, array('min' => 8, 'max' => 255))) {
          $errors[] = "Username must be between 8 and 255 characters.";
        }else if( !$user->has_unique_username($user->username,$user->id ?? 0)){
            $errors[] = "Username not allowed. Try another!";
        }
     
        if(Functions::isblank($user->password)) {
        $errors[] = "Password cannot be blank.";
        } elseif (!Functions::has_length($user->password, array('min' => 12))) {
        $errors[] = "Password must contain 12 or more characters";
        } elseif (!preg_match('/[A-Z]/', $user->password)) {
        $errors[] = "Password must contain at least 1 uppercase letter";
        } elseif (!preg_match('/[a-z]/', $user->password)) {
        $errors[] = "Password must contain at least 1 lowercase letter";
        } elseif (!preg_match('/[0-9]/', $user->password)) {
        $errors[] = "Password must contain at least 1 number";
        } elseif (!preg_match('/[^A-Za-z0-9\s]/', $user->password)) {
        $errors[] = "Password must contain at least 1 symbol";
        }
    
            
        return $errors;
    }

    //função apenas usada nos testes do PHP uni
    public static function deleteUser($email){
        $result= User::deleteByEmail($email);
        return json_encode($result);
    }

    //função para gerar password aleatoriamente
    public static function generatePassword($length){
        $seed = str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()'); // todos os caracteres que a password pode ter
        shuffle($seed); // 'baralhar' o array
        $rand = '';
        foreach (array_rand($seed, $length) as $k) $rand .= $seed[$k];
        $rand.=rand(0,10).'*';//garante que tem 1 numero e 1 caracter especial
        return $rand;
    }
}


?>