<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	include_once("../_database/athsendmail.php");
	include_once("../_database/athtranslate.php");

	// verificação de ACESSO
	// carrega o prefixo das sessions
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	// verificação de acesso do usuário corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"DEL");
	
	// ABERTURA DE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);

	// REQUESTS
	$intCodAtividade= request("vat_todo_cod_atividade");
	$strTitulo		= request("var_todo_titulo");
	$strDescricao	= request("var_todo_descricao");
	$intCategoria	= request("var_todo_categoria");
	$strPrioridade	= request("var_todo_prioridade");
	$strSituacao    = request("var_todo_situacao");
	$strIDResp	    = request("var_todo_id_responsavel");
	$strIDExec		= request("var_todo_id_ult_executor");
	$strPrevDtIni	= request("var_todo_prev_dt_ini");
	$strPrevHrIni1	= request("var_todo_prev_hr_ini_1");
	$strPrevHrIni2	= request("var_todo_prev_hr_ini_2");
	$strPrevHoras1	= request("var_todo_prev_horas_1");
	$strPrevHoras2	= request("var_todo_prev_horas_2");
	$strArqAnexo	= request("var_todo_arquivo_anexo");
	$strLocation	= request("DEFAULT_LOCATION");
	$boolMail		= TRUE;
	
	// REQUESTS ESPECIAIS
	$dtPeriodoIni	= request("var_todo_periodo_inicio");
	$dtPeriodoFim	= request("var_todo_periodo_fim");
	$arrPeriodoMon	= request("var_todo_periodo_meses");
	$intDiaDoMes	= request("var_todo_dias");
	$arrDiasDaSem	= request("var_todo_semana");
	$strOpcaoDias	= request("var_todo_opcao_dias");
	
	$flagOpcaoDias	= request("var_todo_flag_todos_dias");
	$flagPeriodoMon	= request("var_todo_flag_todos_meses");
		
	// TRATAMENTOS PARA INSERÇÕES
	$dtPeriodoIni	= cDate(CFG_LANG,$dtPeriodoIni,false);
	$dtPeriodoFim	= dateAdd("d",1,cDate(CFG_LANG,$dtPeriodoFim,false));
	$arrPeriodoMon 	= ($arrPeriodoMon == "") ? array() : $arrPeriodoMon;
	$arrDiasDaSem 	= ($arrDiasDaSem == "") ? array() : $arrDiasDaSem;
	// $arrDateIni		= explode("-",$dtPeriodoIni);
	// $arrDateFim		= explode("-",$dtPeriodoFim);
	
	// ESTE CONTADOR PODE SER USADO PARA CONTAGEM DE QUANTOS
	// REGISTROS FORAM INSERIDOS, SE POSTERIORMENTE FOR NECES-
	// SÁRIO EXIBIR EM TELA QUANTOS REGISTROS FORAM INSERIDOS
	$auxCounter = 0;
	$strSQL 	= "";
	
	// VARRE A DATA DO INÍCIO AO FIM
	while($dtPeriodoIni != $dtPeriodoFim){
		// EXPLODE DATA INICIAL PARA TRATAMENTOS DE DIAS E DATAS
		$arrCurrDate = explode("-",$dtPeriodoIni);
		
		// VERIFICA SE O MES CORRENTE ESTÁ MARCADO NO MES DE ARRAYS DISPONIVEIS
		if((in_array($arrCurrDate[1],$arrPeriodoMon)) || ($flagPeriodoMon == "S")){
			if(($strOpcaoDias == "DIA") && ($intDiaDoMes == $arrCurrDate[2])){
				// FORMATA A DATA CORRETAMENTE, PARA INSERÇÃO
				$strCurrDate = $arrCurrDate[0]."-".$arrCurrDate[1]."-".$arrCurrDate[2];
				$strSQL = "INSERT INTO tl_todolist (titulo, situacao, prioridade, cod_categoria, id_responsavel, id_ult_executor, prev_dt_ini, prev_hr_ini, prev_horas, arquivo_anexo, descricao, sys_usr_ins, sys_dtt_ins) VALUES ('".prepStr($strTitulo)."','".prepStr($strSituacao)."','".prepStr($strPrioridade)."',".$intCategoria.",'".prepStr($strIDResp)."','".prepStr($strIDExec)."','".prepStr($strCurrDate)."','".$strPrevHrIni1.":".$strPrevHrIni2."','".$strPrevHoras1.":".$strPrevHoras2."','".prepStr($strArqAnexo)."','".prepStr($strDescricao)."','".getsession(CFG_SYSTEM_NAME."_id_usuario")."',CURRENT_TIMESTAMP);";
				// echo($strSQL."<BR /><BR />");
				$auxCounter   = $auxCounter + 1;
			}
			if($strOpcaoDias == "SEM"){
				// CASO O DIA CORRENTE ESTEJA NO ARRAY DE DIAS DA SEMANA DISPONIVEIS
				if(($flagOpcaoDias == "S") || (in_array(getWeekDay($dtPeriodoIni),$arrDiasDaSem))){
					$strCurrDate = $arrCurrDate[0]."-".$arrCurrDate[1]."-".$arrCurrDate[2];
					$strSQL = "INSERT INTO tl_todolist (titulo, situacao, prioridade, cod_categoria, id_responsavel, id_ult_executor, prev_dt_ini, prev_hr_ini, prev_horas, arquivo_anexo, descricao, sys_usr_ins, sys_dtt_ins) VALUES ('".prepStr($strTitulo)."','".prepStr($strSituacao)."','".prepStr($strPrioridade)."',".$intCategoria.",'".prepStr($strIDResp)."','".prepStr($strIDExec)."','".prepStr($strCurrDate)."','".$strPrevHrIni1.":".$strPrevHrIni2."','".$strPrevHoras1.":".$strPrevHoras2."','".prepStr($strArqAnexo)."','".prepStr($strDescricao)."','".getsession(CFG_SYSTEM_NAME."_id_usuario")."',CURRENT_TIMESTAMP);";
					// echo($strSQL."<BR /><BR />");
					$auxCounter   = $auxCounter + 1;
				}
			}
		}
		// EXECUTA SQL
		if($strSQL != ""){
			$objConn->beginTransaction();
			try{
				// INSERT DE TAREFA
				$objConn->query($strSQL);
				$objConn->commit();
			}catch(PDOException $e){
				mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
				$objConn->rollBack();
				die();
			}
		}
		
		// SETA O SQL PARA VAZIO
		$strSQL = "";
			
		// ADICIONA UM DIA PARA A DATA SELECIONADA
		// PARA MAIS UMA VOLTA NO WHILE
		$dtPeriodoIni = dateAdd("d",1,cDate(CFG_LANG,$dtPeriodoIni,false));
		$arrCurrDate  = array();
	}
	
	// DEBUG: QUANTAS VOLTAS DEU
	// echo($auxCounter);
	// die();
		
	// ABRE OBJETO PARA MANIPULAÇÃO NO BANCO
	$objConn = abreDBConn(CFG_DB);
		
    // REDIRECT para a pagina
	redirect($strLocation);
?>