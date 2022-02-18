<?php
include_once("../_database/athdbconn.php");

$objConn = abreDBConn(CFG_DB);

$intCodigo      = request("var_cod_todolist");
$strOper      	= request("var_oper");
$strIdDe        = request("var_de");
$strIdPara      = request("var_para");
$strSituacao    = request("var_situacao");
$strPrioridade  = request("var_prioridade");
$strDtRealizado = cDate(CFG_LANG,request("var_dt_realizado"),false);
$strResposta 	= request("var_resposta");
$strHoras	 	= request("var_horas");

if($intCodigo != ""){
	try{
		$strDtRealizado = ($strDtRealizado != "" && is_date($strDtRealizado)) ? "'" . $strDtRealizado . "'" : "NULL";
		
		$strSQL = " UPDATE tl_todolist SET 
					   id_ult_executor = '" . $strIdPara . "'
					   , situacao      = '" . $strSituacao . "'
					   , prioridade    = '" . $strPrioridade . "'
					   , dt_realizado  = " . $strDtRealizado . " 
					WHERE cod_todolist = " . $intCodigo;
		$objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	try{
		$strSQL = " INSERT INTO tl_resposta (cod_todolist, id_from, id_to, resposta, horas, dtt_resposta, sys_usr_ins) VALUES ( 
					   " . $intCodigo . "
					   ,'" . $strIdDe . "'
					   ,'" . $strIdPara . "' 
					   ,'" . $strResposta . "' 
					   ,'" . $strHoras . "' 
					   ,current_timestamp 
					   ,'" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "' 
					 )";
		$objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
}

redirect("STresposta.php?var_chavereg=" . $intCodigo . "&var_oper=" . $strOper);
?>