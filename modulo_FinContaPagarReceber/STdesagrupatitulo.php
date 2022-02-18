<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");
include_once("../_database/athkernelfunc.php");

//$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
//$arrCodigos = request("var_cod_conta_pagar_receber");
//$strUrlRetorno = request("var_url_retorno");
$intCodDado = request("var_chavereg");
$intCodPJ = request("var_cod_pj");

// abertura de conex�o de objeto ao banco
$objConn = abreDBConn(CFG_DB);

//if(count($arrCodigos) > 1) { 
	//$strCodigos = implode(",",$arrCodigos);
	try {
		// Prepara a execu��o de procedure para
		// agrupamento de titulos
		$objConn->beginTransaction();
		$objStatement = $objConn->prepare("SELECT sp_desagrupa_titulos(:in_cod_titulo,:in_id_usuario);");
		$objStatement->bindParam(":in_cod_titulo",$intCodDado);
		$objStatement->bindParam(":in_id_usuario",getSession(CFG_SYSTEM_NAME . "_id_usuario"));
		$objStatement->execute();
		$objConn->commit();
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
//}
redirect("STifrfinanceiro.php?var_chavereg=".$intCodPJ);
?>