<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));

$strIdUsuario = (request("var_id_usuario") == "") ? getsession(CFG_SYSTEM_NAME . "_id_usuario") : request("var_id_usuario");
$strSenha 	  = request("var_senha");

$objConn = abreDBConn(CFG_DB);

if(getsession(CFG_SYSTEM_NAME . "_id_usuario") != "" && $strSenha != ""){
	try{
		$strSQL = " UPDATE sys_usuario SET senha = md5('" . $strSenha . "') WHERE id_usuario = '" . $strIdUsuario . "'";
		$objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	//mensagem(getTText("senha_inserida_titulo",C_NONE),getTText("senha_inserida_desc",C_NONE),"","javascript:((window.opener != null && window.opener != \'undefined\') ? window.close() : location.href = \'STindex.php\');","standardinfo",1);
	mensagem(getTText("senha_inserida_titulo",C_NONE),getTText("senha_inserida_desc",C_NONE),"","STindex.php","standardinfo",1);
	
}
else{
	mensagem("err_dados_titulo","err_dados_obj_desc","","","erro",1);
}
?>