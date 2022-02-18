<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_scripts/scripts.js");
	include_once("../_scripts/STscripts.js");
	include_once("../_database/athkernelfunc.php");
	
	// REQUESTS
	$intCodDado  = request("var_chavereg");	// COD_HOMOLOGACAO
	
	// CARREGA PREFIX PARA SESSION
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	// VERIFICAÇÃO DE ACESSO
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS_RESP");
		
	if($intCodDado == ""){
		mensagem("err_sql_desc_card","err_envio_ag",getTText("cod_homo_null",C_NONE),"","erro","1");
		die();
	}

	// ABERTURA DE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);
	
	// LOCALIZA O TEXTO PARA IMPRESSÃO
	// ATUALIZA O NÚMERO DE IMPRESSOES
	$objConn->beginTransaction();
	try{
		$strSQL    = "SELECT html_texto FROM sd_homologacao_documento WHERE cod_homologacao_documento = ".$intCodDado;
		$objResult = $objConn->query($strSQL);
		$objRS	   = $objResult->fetch();
		
		$strSQL    = "UPDATE sd_homologacao_documento SET qtde_impresso = qtde_impresso + 1 WHERE cod_homologacao_documento = ".$intCodDado;
		$objConn->query($strSQL);
		
		$objConn->commit();
	}catch(PDOException $e){
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	echo(html_entity_decode(getValue($objRS,"html_texto")));
?>