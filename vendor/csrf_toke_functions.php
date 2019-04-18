<?php
//deve se chamar o  antes de carregar esta página
//session_start();->esta a ser carregada no boot
//Gerar um token para usar com CSRF protection
//nao deve guardar o token
function csrf_token(){
    return md5(uniqid(rand(),TRUE));
}

//gerar e guardar o token CSRF no user session
//exige que a sessão ja tinha sido inicializada
function create_csrf_token(){
    $token = csrf_token();
    return $token;
}

//destroi um token removendoa da sessão
function destroy_csrf_token(){
    $_SESSION['csrf_token'] = null;
    $_SESSION['csrf_token_time'] = null;
    return true;
}

//retorna verdadeiro se o token post user-submitted é identico ao 
//que foi guardado anteriormente na session.
//senao retorna falso
function csrf_token_is_valid($csrf){
    try{
        if(isset($csrf)){
            $user_token = $csrf; 
            $stored_token = isset($_SESSION['csrf_token'])? $_SESSION['csrf_token']:null;
            return $user_token === $stored_token;
        }else{
            return false;
        }
    }catch(Exception $e){
        return json_encode($e->getMessage());
    }
}

//podemos verificar a validade do token e tratar da falha ou usar esta
//função "stop-everything-on-failure"
function die_on_csrf_token_failure(){
    if(!csrf_token_is_valid()){
        die("CSRF token validation failed.");
    }
}

//verificar se o token é recente
function csrf_token_is_recent(){
    $max_elapsed=60*60*24;//1 dia
    if(isset($_SESSION['csrf_token_time'])) {
		$stored_time = $_SESSION['csrf_token_time'];
		return ($stored_time + $max_elapsed) >= time();
	} else {
		// Remove expired token
		destroy_csrf_token();
		return false;
	}
}

?>