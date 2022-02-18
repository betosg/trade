<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athsendmail.php");

	// ABERTURA DE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);

	// REQUESTS
	$strIDUSER  = request("var_id_usuario");
	$strEMail   = request("var_email");
	$strEmpresa = request("var_empresa");
	$intCodDado = request("var_cod_pj");
	$strOBS		= request("var_obs_cancelamento");
	
	// DELETA A PJ E O RESPECTIVO USUÁRIO QUE INS
	// E POR CASCATA, TRIGGER DE BEF DEL DO CAD_PJ
	// FAZ INSERT NA TABELA DE LOG DE PJS CANCELADAS
	$objConn->beginTransaction();
	try{
		$strSQL  = " UPDATE cad_pj SET obs = '".$strOBS."' WHERE cod_pj = ".$intCodDado;
		$objConn->query($strSQL);
		
		$strSQL  = " DELETE FROM cad_pj WHERE cod_pj = ".$intCodDado;
		$objConn->query($strSQL);
		
		$strSQL  = " DELETE FROM sys_usuario WHERE id_usuario = '".$strIDUSER."'";
		$objConn->query($strSQL);
		
		$objConn->commit();
	} catch(PDOException $e){
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_titulo",$e->getMessage(),"","erro",1);
		die();
	}
	
	// Encaminha EMAIL informando que CADASTRO foi REJEITADO
	$strEMAILBODY = getVarEntidade($objConn, "msg_associado_cadastro_nao_aprovado");
	if ($strEMAILBODY != "") {
		$strEMAILBODY = str_replace("<TAG_EMPRESA/>", $strEmpresa, $strEMAILBODY);
		$strEMAILBODY = str_replace("<TAG_OBS/>"    , $strOBS    , $strEMAILBODY);
		
		// ENVIA EMAIL
		emailNotify($strEMAILBODY, getTText("novo_cadastro_nao_aprovado",C_UCWORDS), $strEMail, CFG_EMAIL_SENDER);
	}
	
	// DESTRÓI OBJETO DE CONEXÃO
	$objConn = NULL;
	
	// REDIRECT PARA A PÁGINA PRINCIPAL
	redirect("STindex.php");
?>