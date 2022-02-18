<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_database/athsendmail.php");
	include_once("../_database/athtranslate.php");
	
	// ABRE CONEXÃO COM DATABASE
	$objConn = abreDBConn(CFG_DB);
	
	// REQUESTS
	$intCodDado  = request("var_chavereg");
	$strREDIRECT = request("var_redirect");
	
	// LOCALIZA AGENDA, SEU TIPO E CODIGO
	try{
		$strSQL    = "SELECT ag_agenda.tipo, ag_agenda.codigo FROM ag_agenda WHERE cod_agenda = ".$intCodDado;
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// Fetch na linha encontrada
	$objRS     = $objResult->fetch();
	$strTIPO   = getValue($objRS,"tipo");
	$intCODIGO = getValue($objRS,"codigo");
	
	// Switch no TIPO para montar o link
	switch(strtoupper($strTIPO)){
		case "SD_HOMOLOGACAO":
			$strLINK = "../modulo_PainelAdmin/STConfirmaHomo.php?var_chavereg=".$intCODIGO."&var_redirect=".$strREDIRECT;
		break;
		
		default:
			mensagem("err_sql_titulo","err_sql_desc_card",getTText("nenhum_link",C_NONE),$strREDIRECT,"aviso",1);
			die();
		break;
	}
	
	redirect($strLINK);
?>