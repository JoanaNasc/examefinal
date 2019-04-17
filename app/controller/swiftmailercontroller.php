<?php
namespace app\controller;

class SwiftMailerController{

  
    private static $smtp_server='smtp.gmail.com';
    private static $username='academiaphp2019@gmail.com';
    private static $password='academiaphp2019';
    

    public static function sendEmail($email,$messageHtml,$title){
        try{
            $from = ['academiaphp2019@gmail.com'=>'Joana Nascimento'];
            $to = [$email => $email];
            //preparar a mensagem do e-mail
            $message = new \Swift_Message();

            //adicionar o titulo do e-mail 
            $message->setSubject($title);

            //adicionar o e-mail de que se quer enviar 
            $message->setFrom($from);

            //adicionar o e-mail para onde queremos enviar
            $message->setTo($to);

            //adicionar o corpo do e-mail
            $message->setBody($messageHtml,'text/html');

            //criar o transport smtpTransport
            $transport = new \Swift_SmtpTransport(self::$smtp_server,465,'ssl');
            $transport->setUsername(self::$username);
            $transport->setPassword(self::$password);

            $mailer=new \Swift_Mailer($transport);
            $result = $mailer->send($message);
            if($result){
                return true;
            }else{
                return false;
            }
        }catch(Exception $e){
            echo $e->getMessage();
        }
    }
}

?>