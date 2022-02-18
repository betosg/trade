<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");

	// verificação de ACESSO
	// carrega o prefixo das sessions
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	// verificação de acesso do usuário corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"DEL");

	// REQUESTS
	$intCodAgenda	= request("var_chavereg");	// cod_agenda para o qual resposta irá ser relacionada
	$strTitulo		= request("var_titulo");	// titulo da agenda	
	$strDescricao	= request("var_descricao");	// descricao da agenda
	$strCategoria	= request("var_categoria");	// categoria da agenda
	$strPrioridade	= request("var_prioridade");// prioridade da agenda
	$strLocation	= request("DEFAULT_LOCATION");
	$strFlagRedir	= request("var_flag_redir");
	
	// request das datas - especial
	$auxDtPrevIni	= request("var_dt_prev_ini");												  // data prev inicio
	$auxHrPrevIni	= (substr(request("var_hr_prev_ini"),0,2) >= 24) ? "00".
					   substr(request("var_hr_prev_ini"),2,4).":00" : request("var_hr_prev_ini"); // hr prev inicio
	$auxDtPrevFim	= request("var_dt_prev_fim");												  // data prev fim
	$auxHrPrevFim	= (substr(request("var_hr_prev_fim"),0,2) >= 24) ? "00".
					   substr(request("var_hr_prev_fim"),2,4).":00" : request("var_hr_prev_fim"); // hr prev fim
	$auxDtRealizado	= request("var_dt_realizado");												  // data prev fim
	$auxHrRealizado	= (substr(request("var_hr_realizado"),0,2) >= 24) ? "00".
					   substr(request("var_hr_realizado"),2,4).":00" : request("var_hr_realizado"); // hr realizado
	
	// formatação das datas de previsão
	// de início e fim, respectivamente
	$dtPrevIni	= ($auxDtPrevIni   == "") ? "" : $auxDtPrevIni   ." ". $auxHrPrevIni; // data formatada [PREV_DTT_INI]
	$dtPrevFim	= ($auxDtPrevFim   == "") ? "" : $auxDtPrevFim   ." ". $auxHrPrevFim; // data formatada [PREV_DTT_FIM]
	$dtPrevReal	= ($auxDtRealizado == "") ? "" : $auxDtRealizado ." ". $auxHrRealizado; // data formatada [DTT_REALIZADO]
	$dtPrevIni	= cDate(CFG_LANG,$dtPrevIni,true); 	 // data formatada [PREV_DTT_INI]
	$dtPrevFim	= cDate(CFG_LANG,$dtPrevFim,true); 	 // data formatada [PREV_DTT_FIM]
	$dtPrevReal	= cDate(CFG_LANG,$dtPrevReal,true);  // data formatada [DTT_REALIZADO]
	
	// cod_agenda nao pode vir vazio
	if($intCodAgenda == ""){
		mensagem("err_sql_desc_card","err_envio_ag",getTText("agenda_cod_null",C_NONE),$strRedirect,'erro','1');
		die();
	}
	
	// consistencia para campos vazios
	$strErrMsg = "";
	$strErrMsg .= ($strTitulo  	  == "") ? getTText("titulo_vazio",C_NONE)    ."<br />" : "";
	// $strErrMsg .= ($strDescricao  == "") ? getTText("descricao_vazio",C_NONE) ."<br />"	: "";
	$strErrMsg .= ($strCategoria  == "") ? getTText("categoria_vazio",C_NONE) ."<br />" : "";
	$strErrMsg .= ($strPrioridade == "") ? getTText("prioridade_vazio",C_NONE)."<br />" : "";
	$strErrMsg .= ($dtPrevIni=="" || $dtPrevIni==" " || !is_date($dtPrevIni)) ? getTText("dtt_ini_invalido",C_NONE)."<br />" : "";
	$strErrMsg .= ($dtPrevFim=="" || $dtPrevFim==" " || !is_date($dtPrevFim)) ? getTText("dtt_fim_invalido",C_NONE)."<br />" : "";
	$strErrMsg .= (($dtPrevFim < $dtPrevIni) && (is_date($dtPrevFim) && is_date($dtPrevIni)))
				  ? getTText("dtt_fim_maior",C_NONE)."<br />" : "";
	$strErrMsg .= ($auxDtRealizado == "" && $auxHrRealizado != "") ? "Informe a data, caso queira fechar a agenda" : "";
	$strErrMsg .= ($auxDtRealizado != "" && $auxHrRealizado == "") ? "Informe a hora, caso queira fechar a agenda" : ""; 
	$strErrMsg  = ($strErrMsg != "") ? getTText("campos_nao_informados",C_NONE)."<br /><br />".$strErrMsg : $strErrMsg;
	
	if($strErrMsg != ""){
		mensagem("err_dados_titulo","err_dados_submit_desc",$strErrMsg,
				 'STupdevent.php?var_chavereg='.$intCodAgenda."&var_redirect=".$strLocation,'aviso','1');
		die();
	}
	
	// abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
		
	// UPDATE NA TABELA DE AGENDAS / EVENTOS e
	// depois redirect para a pagina da agenda
	try{
		$strSQL = "
			UPDATE ag_agenda SET
				  titulo 	   = '".prepStr($strTitulo)."'
				, descricao    = '".prepStr($strDescricao)."'
				, categoria    = '".prepStr($strCategoria)."'
				, prioridade   = '".prepStr($strPrioridade)."'
				, prev_dtt_ini = '".$dtPrevIni."'
				, prev_dtt_fim = '".$dtPrevFim."'
				, sys_usr_upd  = '".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."'
				, sys_dtt_upd  = CURRENT_TIMESTAMP 
			WHERE cod_agenda = ".$intCodAgenda;
		$objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	if($dtPrevReal != ""){
		// Update na tabela caso REALIZADO
		try{
			$strSQL = "	UPDATE ag_agenda SET dtt_realizado = '".$dtPrevReal."' WHERE cod_agenda = ".$intCodAgenda;
			$objConn->query($strSQL);
		}catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			die();
		}
	}
	
	// REDIRECT para a pagina
	if($strFlagRedir != ""){ redirect("STdatascheduler.php"); }
	else{ redirect($strLocation); }
?>