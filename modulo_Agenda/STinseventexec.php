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
	$strTitulo		= request("var_titulo");	   // Titulo da agenda	
	$strDescricao	= request("var_descricao");	   // Descricao da agenda
	$strCategoria	= request("var_categoria");	   // Categoria da agenda
	$strPrioridade	= request("var_prioridade");   // Prioridade da agenda
	$strGrpUser	    = request("var_grp_user");     // Grupo de usuários para serem inseridos como CITADOS do EVENTO na AGENDA
	$strLocation	= request("DEFAULT_LOCATION");
	
	// REQUEST DATAS - ESPECIAL
	$auxDtPrevIni	= request("var_dt_prev_ini");												  // data prev inicio
	$auxHrPrevIni	= (substr(request("var_hr_prev_ini"),0,2) >= 24) ? "00".substr(request("var_hr_prev_ini"),2,4).":00" : request("var_hr_prev_ini"); // HR prev inicio
	$auxDtPrevFim	= request("var_dt_prev_fim");												  // data prev fim
	$auxHrPrevFim	= (substr(request("var_hr_prev_fim"),0,2) >= 24) ? "00".substr(request("var_hr_prev_fim"),2,4).":00" : request("var_hr_prev_fim"); // hr prev fim
	
	// Formatação das datas de previsão
	// de início e fim, respectivamente
	$dtPrevIni	= ($auxDtPrevIni == "") ? "" : $auxDtPrevIni ." ". $auxHrPrevIni; // data formatada [PREV_DTT_INI]
	$dtPrevFim	= ($auxDtPrevFim == "") ? "" : $auxDtPrevFim ." ". $auxHrPrevFim; // data formatada [PREV_DTT_FIM]
	$dtPrevIni	= cDate(CFG_LANG,$dtPrevIni,true); 	 // data formatada [PREV_DTT_INI]
	$dtPrevFim	= cDate(CFG_LANG,$dtPrevFim,true); 	 // data formatada [PREV_DTT_FIM]
	
	// Consistencia para campos vazios
	$strErrMsg = "";
	$strErrMsg .= ($strTitulo  	  == "") ? getTText("titulo_vazio",C_NONE)    ."<br />" : "";
	// $strErrMsg .= ($strDescricao  == "") ? getTText("descricao_vazio",C_NONE) ."<br />"	: "";
	$strErrMsg .= ($strCategoria  == "") ? getTText("categoria_vazio",C_NONE) ."<br />" : "";
	$strErrMsg .= ($strPrioridade == "") ? getTText("prioridade_vazio",C_NONE)."<br />" : "";
	$strErrMsg .= (($auxHrPrevIni == "") || (strlen($auxHrPrevIni) < 5)) ? getTText("hr_ini_invalido",C_NONE)."<br />" : "";
	$strErrMsg .= (($auxHrPrevFim == "") || ($auxHrPrevFim == "") || (strlen($auxHrPrevFim) < 5)) ? getTText("hr_fim_invalido",C_NONE)."<br />" : "";
	$strErrMsg .= ($dtPrevIni=="" || $dtPrevIni==" " || !is_date($dtPrevIni)) ? getTText("hr_ini_invalido",C_NONE)."<br />" : "";
	$strErrMsg .= ($dtPrevIni=="" || $dtPrevIni==" " || !is_date($dtPrevIni)) ? getTText("dtt_ini_invalido",C_NONE)."<br />" : "";
	$strErrMsg .= ($dtPrevFim=="" || $dtPrevFim==" " || !is_date($dtPrevFim)) ? getTText("dtt_fim_invalido",C_NONE)."<br />" : "";
	$strErrMsg .= (($dtPrevFim < $dtPrevIni) && (is_date($dtPrevFim) && is_date($dtPrevIni))) ? getTText("dtt_fim_maior",C_NONE)."<br />" : "";
	$strErrMsg  = ($strErrMsg != "") ? getTText("campos_nao_informados",C_NONE)."<br /><br />".$strErrMsg : $strErrMsg;
	
	if($strErrMsg != ""){
 		mensagem("err_dados_titulo","err_dados_submit_desc",$strErrMsg,'javascript:window.history.back();','aviso','1');
		die();
	}
	
	// abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
		
	// INSERT NA TABELA DE AGENDAS / EVENTOS e
	// depois redirect para a pagina da agenda
	$objConn->beginTransaction();
	try{
		$strSQL = "
			INSERT INTO ag_agenda( 
				  titulo
				, descricao
				, categoria
				, prioridade
				, prev_dtt_ini
				, prev_dtt_fim
				, id_responsavel
				, sys_usr_ins
				, sys_dtt_ins
			) VALUES (
				  '".prepStr($strTitulo)."'
				, '".prepStr($strDescricao)."'
				, '".prepStr($strCategoria)."'
				, '".prepStr($strPrioridade)."'
				, '".$dtPrevIni."'
				, '".$dtPrevFim."'
				, '".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."'
				, '".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."'
				, CURRENT_TIMESTAMP)";
		$objConn->query($strSQL);

		if ($strGrpUser<>"") {
			$strSQL  = " INSERT INTO ag_agenda_citado (cod_agenda,id_usuario,sys_usr_ins,sys_dtt_ins) ";
			$strSQL .= " SELECT (SELECT last_value FROM ag_agenda_cod_agenda_seq)";
			$strSQL .= "      ,id_usuario ";
			$strSQL .= "      ,'" . prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario")) . "' ";
			$strSQL .= "      ,CURRENT_TIMESTAMP ";
			$strSQL .= " FROM sys_usuario ";
			$strSQL .= " WHERE sys_usuario.grp_user = '" . $strGrpUser . "' ";
			$strSQL .= " AND dtt_inativo IS NULL ";
			$objConn->query($strSQL);
		}	

		$objConn->commit();
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollBack();
		die();
	}
	
    // REDIRECT para a pagina
	redirect($strLocation);
?>