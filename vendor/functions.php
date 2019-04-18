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
    

    /***
     * FUNÇÕES PARA O UPLOAD DO FICHEIRO EM SEGURANÇA
     * 
     */
   /*
	Executa o upload atraves de uma serie de validações
	se o arquivo passar nas validaçoes vai para a directoria de upload
	e retira as permissoes de execução
    */
    function upload_file($field_name) {
       
        $upload_path =dirname(dirname(__FILE__)).'/resources/receivedFiles/';
        $max_file_size=1024;//1kb expressed in bytes
        
        if(isset($_FILES[$field_name])) {
            echo 'inside if(isset($_FILES[$field_name])) ';
            $file_name = sanitize_file_name($_FILES[$field_name]['name']);

            $path_parts = pathinfo($file_name);
            $file_extension = $path_parts['extension'];

            $file_type = $_FILES[$field_name]['type'];
            $tmp_file  = $_FILES[$field_name]['tmp_name'];
            $error 		 = $_FILES[$field_name]['error'];
            $file_size = $_FILES[$field_name]['size'];

            $file_path = $upload_path .'/'.$file_name;

            if($error > 0) {
                // Display errors caught by PHP
                return json_encode("Error: " . file_upload_error($error));
            
            } elseif( !is_uploaded_file($tmp_file)){
                return json_encode("Error:Does not reference a recently upload file.<br/>");
            }else if($file_size>$max_file_size){
                return json_encode("Error: File size is too big.<br />");
    
            } elseif(strcmp($file_extension, 'csv') !== 0){
                //verifica mos se a extensão do ficheiro é csv se não for  nao deixa fazer upload
                return json_encode("Error: Not an allowed file extension.<br />");
            
            } elseif(file_exists($file_path)){
                return json_encode("Error:A file with that name already exists in target location.<br/>");
            }else {
                // Success!
                $message = "File was uploaded without errors.<br />";
                $message .= "File name is '{$file_name}'.<br />";
                $message .= "Uploaded file size was {$file_size} bytes.<br />";

                // filesize() is most useful when not working with uploaded files.
                $tmp_filesize = filesize($tmp_file); // always in bytes
                //mover para a pasta correcta
                if(move_uploaded_file($tmp_file,$file_path)){
                    $message .= "File moved to:{$file_path}.<br/>";
                }
                //restringir permissoes do ficheiro
                if(chmod($file_path,0644)){
                    $message .= "Execute permissions removed from file.<br/>";
                    $file_permissions = file_permissions($file_path);
                    $message .= "File permissions are now '{$file_permissions}'.<br/>";
                }else{
                    $message .= "Error:Execute permissions could not be removed.<br/>";
                }
                return json_encode($message);
            }
        }
    }
    /*alterar as permissoes do ficheiro*/
    function file_permissions($file){
        $numeric_perms = fileperms($file);//retorna um valor numerico
        $octal_perms = sprintf('%o',$numeric_perms);//transforma em octal
        return substr($octal_perms,-4);
    }

    function sanitize_file_name($filename){
        $filename = preg_replace("/[^A-Za-z0-9_\-\.]|[\.]{2}/","",$filename);
        $filename = basename($filename);
        return $filename;
    }

    //verifica se contem php
    function file_contains_php($file){
        $contents = file_get_contents($file);
        $position = strpos($contents,'<?php');
        return $position!==false;
    }
    //Fornece mensagens de erro 
    // para os erros de upload de arquivo.
    function file_upload_error($error_integer) {
        $upload_errors = array(
            // http://php.net/manual/en/features.file-upload.errors.php
            UPLOAD_ERR_OK 				=> "No errors.",
            UPLOAD_ERR_INI_SIZE  	=> "Larger than upload_max_filesize.",
        UPLOAD_ERR_FORM_SIZE 	=> "Larger than form MAX_FILE_SIZE.",
        UPLOAD_ERR_PARTIAL 		=> "Partial upload.",
        UPLOAD_ERR_NO_FILE 		=> "No file.",
        UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
        UPLOAD_ERR_CANT_WRITE => "Can't write to disk.",
        UPLOAD_ERR_EXTENSION 	=> "File upload stopped by extension."
        );
        return $upload_errors[$error_integer];
    }
}
?>