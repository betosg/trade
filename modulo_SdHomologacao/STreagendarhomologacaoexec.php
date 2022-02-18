<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	
	// REQUESTS
	$intCodDado   = request("var_chavereg");	// COD_AGENDA
	$strLOCATION  = request("DEFAULT_LOCATION");
	$flagTITULO   = request("var_flag_alterar_titulo");
	$dttVCTO	  = request("var_dt_vcto_tit");
	$intCodTITULO = request("var_titulo");
	
	// REQUEST DAS DATAS
	$auxDtPrevIni	= request("var_new_prev_dtt_ini");												  // data prev inicio
	$auxHrPrevIni	= (substr(request("var_hr_prev_ini"),0,2) >= 24) ? "00".substr(request("var_hr_prev_ini"),2,4).":00" : request("var_hr_prev_ini"); // hr prev inicio
	$auxDtPrevFim	= request("var_new_prev_dtt_fim");												  // data prev fim
	$auxHrPrevFim	= (substr(request("var_hr_prev_fim"),0,2) >= 24) ? "00".substr(request("var_hr_prev_fim"),2,4).":00" : request("var_hr_prev_fim"); // hr prev fim
		
	// FORMATAÇÃO DAS DATAS DE INÍCIO E FIM
	$dtPrevIni	= ($auxDtPrevIni   == "") ? "" : $auxDtPrevIni   ." ". $auxHrPrevIni; // data formatada [PREV_DTT_INI]
	$dtPrevFim	= ($auxDtPrevFim   == "") ? "" : $auxDtPrevFim   ." ". $auxHrPrevFim; // data formatada [PREV_DTT_FIM]
	$dtPrevIni	= cDate(CFG_LANG,$dtPrevIni,true); 	 // data formatada [PREV_DTT_INI]
	$dtPrevFim	= cDate(CFG_LANG,$dtPrevFim,true); 	 // data formatada [PREV_DTT_FIM]
		
	// VERIFICAÇÃO DE DATA INVÁLIDA
	$strErrMsg  = "";
	$strErrMsg .= ($dtPrevIni == "" || $dtPrevIni == " " || !is_date($dtPrevIni)) ? getTText("dtt_ini_invalido",C_NONE)."<br />" : "";
	$strErrMsg .= ($dtPrevFim == "" || $dtPrevFim == " " || !is_date($dtPrevFim)) ? getTText("dtt_fim_invalido",C_NONE)."<br />" : "";
	$strErrMsg .= (($dtPrevFim < $dtPrevIni) && (is_date($dtPrevFim) && is_date($dtPrevIni))) ? getTText("dtt_fim_maior",C_NONE)."<br />" : "";
	$strErrMsg  = ($strErrMsg != "") ? getTText("campos_nao_informados",C_NONE)."<br /><br />".$strErrMsg : $strErrMsg;
	
	// CASO MSG_ERRO != ''
	if($strErrMsg != ""){
		mensagem("err_dados_titulo","err_dados_submit_desc",$strErrMsg,$strLOCATION,'aviso','1');
		die();
	}
	
	// ABERTURA DE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);
		
	// FAZ UPDATE NA DATA, ATÉ AQUI PRESUME-SE QUE TUDO OCORREU NORMALMENTE
	try{
		$strSQL = " UPDATE ag_agenda SET prev_dtt_ini = '".$dtPrevIni."', prev_dtt_fim = '".$dtPrevFim."' WHERE cod_agenda = ".$intCodDado;
		//die($strSQL);
		$objResult  = $objConn->query($strSQL);
		$objRS      = $objResult->fetch();
		
		if($flagTITULO != ""){
			$strSQL    = " UPDATE fin_conta_pagar_receber SET dt_vcto = '".cDate(CFG_LANG,$dttVCTO,false)."' WHERE cod_conta_pagar_receber = ".$intCodTITULO;
			$objResult = $objConn->query($strSQL);
		}
		
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	redirect($strLOCATION);
?>