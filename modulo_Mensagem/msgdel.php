<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$strCodigos = request("var_chavereg");

$objConn = abreDBConn(CFG_DB);

$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), $strOperacao);

try{
	$objConn->query(" DELETE FROM msg_mensagem WHERE cod_mensagem IN (" . $strCodigos . ") ");
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

redirect(getsession($strSesPfx . "_grid_default"));
?>