<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

//$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));

$strIdUsuario = request("var_id_usuario");
$strSenha 	   = request("var_senha");
$key           =   base64_decode(request("xy"));

if ($key != ""){
	$arrKey = explode(":", $key);
	$db = $arrKey[1];
	$strIdUsuario = $arrKey[0];
}else {$db="";}

if ($db == ""){
	
	$objConn = abreDBConn(CFG_DB);
}else{$objConn = abreDBConn($db);}
//echo "<br>". $strIdUsuario ;
//echo "<br>". $strSenha 	   ;
//echo "<br>". $db           ;
if($strIdUsuario != "" && $strSenha != ""){
	try{
	 	$strSQL = " UPDATE sys_usuario SET senha = md5('" . $strSenha . "') WHERE id_usuario = '" . $strIdUsuario . "'";
	//die();
	$objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	//mensagem(getTText("senha_inserida_titulo",C_NONE),getTText("senha_inserida_desc",C_NONE),"","javascript:((window.opener != null && window.opener != \'undefined\') ? window.close() : location.href = \'STindex.php\');","standardinfo",1);
	if ($key != ""){
		mensagem("SENHA ALTERADA","<a href='https://tradeunion.proevento.com.br/abfm/'>Clique aqui para acessar o sistema com sua nova senha</a>","","","standardinfo",1);
	}else{
		mensagem("SENHA ALTERADA","Sua senha foi alterada com sucesso.","","","standardinfo",1);
	}
}
else{
	mensagem("err_dados_titulo","err_dados_obj_desc","","","erro",1);
}
?>