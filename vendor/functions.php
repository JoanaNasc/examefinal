<?php
namespace vendor;

/*
    Classe auxiliar com os metodos necessários para fazer validações dos campos

*/

class Functions {

    /*
        Verifica se o campo passado por parametro vem vazio
            - usa o trim para não haver possibilidade de vir com o caracter "espaço"
    */

    public static function isblank($value){
        return !isset($value) || trim($value) === '';
    }

    /*
        Verifica se o campo passado por parametro está dentro dos valores aceitaveis para esse campo, usa um array
        options com os valores min e max para fazer essa verificação
    
    */

    public static  function has_length($value, $options) {
        
        if(isset($options['min']) && !(strlen($value) > ($options['min'] - 1))) {
            //verifica se o valor é maior que o minimo aceitavel se nao for retorna false
            return false;
        } elseif(isset($options['max']) && !(strlen($value)< $options['max'] + 1)) {
            //verifica se o valor é maior que o maximo aceitavel se nao for retorna false
            return false;
        } else {
            //se nao entrar nos 2 ifs anteriores quer dizer que é maior que o minimo e menor que o maximo, logo retorna true
            return true;
        }
    }

    /*
        utiliza a funcção filter_var com o filtro FILTER_VALIDATE_EMAIL
    */
    public static function  validateEmail($value){
        $validate=filter_var($value, FILTER_VALIDATE_EMAIL);
        //valida email, se houver erro retorna o erro na variavel de sessao errors
        return $validate;
    }

    /*
        função para enviar o email com a messagem e o token de confimaçao
    */
    /*public static function sendConfirmationEmail($email,$message){
        $to = $email;  // email para onde vai ser enviado
        $subject = 'Please check your email';
        $headers = 'From: jnascimento@growin.pt';  // Replace with a valid address on your domain
       
        $mailSent = mail($to, $subject, $message, $headers);
        return $mailSent;
    }*/
    

    /*
        função para enviar a mensagem para o slack com a messagem e o token de confimaçao
    */
    public static function messageToSlack($message){
        
        $sURL = "https://slack.com/api/chat.postMessage"; // The POST URL

        $data = http_build_query([
            "token" =>
            "xoxb-101259630004-520202224629-DPHnA8VClMhljp6nkOQzR5BH",
            "channel" => "#botecho", // Canal para onde querem enviar
            "text" => "(Joana)Confirm Email send in" . date("H:i:s") ."\n" .$message,
            "username" => "AcademiaPHP Bot",
        ]);
        $aHTTP = array(
            'http' => // The wrapper to be used
            array(
            'method'  => 'POST', // Request Method
            // Request Headers Below

            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $data
            )
        );
        //cria um conjunto de informações referentes a um pedido http
        $context = stream_context_create($aHTTP);
        //faz o pedido http para o URL definido em cima com o contexto que foi criado com a função anterior
        // como nao temos path criamos a função a false
        $contents = file_get_contents($sURL, false, $context);
    }

    /*
        funçao para zipar o projecto
    */
    function zipProject(){
        //pasta a ser zipada
        $rootFolder = PROJECT_PATH;

        //nome do zip com extensão
        $zipName ='Joana'.date("YmdHis").'.zip';
        //criação do zip
        $zip= New ZipArchive();
        $zip->open(SAFE_FILES_PATH.'/'.$zipName);

        //cria o ficheiro zip, caso já esteja criado reescreve o ficheiro.
        $zipCreated = $zip->open(SAFE_FILES_PATH.'/'.$zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        //caso de erro
        if(!$zipCreated){
            echo 'Zip creation failed, code:' . $zipCreated;
            return false;
        }


        //percorre a pasta recursivamente à procura dos ficheiros que lá existam para os puder adicionar ao zip
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootFolder),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        //criação do zip de forma recursiva
        foreach ($files as $file) {
            //só adiciona no zip os ficheiros, ignora as directorias.
            if(!$file->isDir()){
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($rootFolder));
                    $zip->addFile($filePath, $relativePath);
            }
            
        }

        $zip->close();
        return true; 
    }

}
?>