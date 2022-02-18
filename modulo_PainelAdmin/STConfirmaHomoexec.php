<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	
	// REQUESTS
	$intCodDado  = request("var_chavereg");
	$intCodPJPF  = request("var_cod_pj_pf");
	$dtDemissao  = request("var_dt_demissao");
	$strObs 	 = request("var_obs");
	$intCodPF 	 = request("var_cod_pf_card");
	$strREDIRECT = request("var_redirect");
	$strOperacao = request("var_oper");     
	$strExec     = request("var_exec");     
	$strPopulate = request("var_populate"); 
	$strAcao   	 = request("var_acao");     
	
	// CONTROLE DE ACESSO
	if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos ítens do módulo
	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));
	
	// Controle para DATA DE DEMISSÃO
	$dtDemissao = cDate(CFG_LANG, $dtDemissao, false);
	$strMsg 	= "";
	if($intCodDado 	== "") $strMsg .= "Homologação inválida<br>";
	if(($dtDemissao == "") || (!is_date($dtDemissao))) $strMsg .= "Data de Demissão inválida<br>";
	
	// Verifica se data informada é uma data futura
	$tsData1 = strtotime($dtDemissao);
	$tsData2 = strtotime(date("Y-m-d"));//dDate(CFG_LANG,now(),true);
		
	if($tsData1 > $tsData2) $strMsg .= "Data de Demissão futura<br>";
	if($strMsg != ""){  
		mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "", "erro", 1);
		die();
	}
	
	// ABERTURA DE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);
	
	// TRANSAÇÃO
	$objConn->beginTransaction();
	try{
		// Marca a data de homologação
		$strSQL  = " UPDATE sd_homologacao ";
		$strSQL .= " SET dtt_homologacao = '".$dtDemissao."' ";
		$strSQL .= "   , usr_homologacao = '" . getSession(CFG_SYSTEM_NAME . "_id_usuario") . "' ";
		$strSQL .= "   , sys_dtt_upd = CURRENT_TIMESTAMP ";
		$strSQL .= "   , sys_usr_upd = '" . getSession(CFG_SYSTEM_NAME . "_id_usuario") . "' ";
		$strSQL .= "   , obs = '" . $strObs . "' ";
		$strSQL .= " WHERE cod_homologacao = ".$intCodDado;
		$objConn->query($strSQL);
		
		// Marca a data de demissao
		$strSQL  = " UPDATE relac_pj_pf ";
		$strSQL .= " SET dt_demissao = '" . $dtDemissao . "' ";
		$strSQL .= "   , sys_dtt_upd = CURRENT_TIMESTAMP ";
		$strSQL .= "   , sys_usr_upd = '" . getSession(CFG_SYSTEM_NAME . "_id_usuario") . "' ";
		$strSQL .= " WHERE cod_pj_pf = ".$intCodPJPF;
		$objConn->query($strSQL);
		
		// Verifica se PF está relacionada
		// em mais de uma empresa, caso ñ
		// então marca PF como INATIVA
		// PF tem de estar DT_DEMISSAO NULL
		$strSQL    = "SELECT dt_demissao FROM relac_pj_pf WHERE cod_pf = ".$intCodPF." AND dt_demissao IS NULL";
		$objResult = $objConn->query($strSQL);
		if($objResult->rowCount() <= 0){
			$strSQL = "UPDATE cad_pf SET 
						  dtt_inativo = CURRENT_TIMESTAMP
						, sys_usr_upd = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."' 
						, sys_dtt_upd = CURRENT_TIMESTAMP
					   WHERE cod_pf = ".$intCodPF;
			$objConn->query($strSQL);
		}
		
		// Inativa credenciais
		$strSQL  = " UPDATE sd_credencial ";
		$strSQL .= " SET sys_dtt_upd = CURRENT_TIMESTAMP ";
		$strSQL .= " , dtt_inativo = CURRENT_TIMESTAMP ";
		$strSQL .= " , sys_usr_upd = '" . getSession(CFG_SYSTEM_NAME . "_id_usuario") . "' ";
		$strSQL .= " WHERE cod_pj_pf = ".$intCodPJPF;
		$objConn->query($strSQL);
		
		// COMMIT NA TRANSAÇÃO
		$objConn->commit();
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollback();
		die();
	}
	
	// DESTRÓI OBJETO DE CONEXÃO COM DB
	$objConn = NULL;
	
	if($strREDIRECT != ""){ redirect($strREDIRECT); }
	else { redirect("STindex.php"); }
?>