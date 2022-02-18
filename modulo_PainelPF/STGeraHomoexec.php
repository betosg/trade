<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	
	// REQUESTS
	$strMsg 	 = "";
	$intCodDado  = request("var_chavereg");
	$intCodPJ 	 = request("var_cod_pj");
	$intCodPF 	 = request("var_cod_pf");
	$strNome 	 = request("var_nome");
	$strCPF 	 = request("var_cpf");
	$strObs 	 = request("var_obs");
	$strRedirect = (request("var_redirect") == "") ? "STColabAtivos.php" : request("var_redirect");
	$strPopulate = request("var_populate"); // Flag para necessidade de popular o session ou não
	
	// REQUEST DATAS - ESPECIAL
	$auxDtPrevIni	= request("var_dtt_ped_agendamento_homo");												  // data prev inicio
	$auxHrPrevIni	= (substr(request("var_ped_hr_agendamento_homo"),0,2) >= 24) ? "00".substr(request("var_ped_hr_agendamento_homo"),2,4).":00" : request("var_ped_hr_agendamento_homo"); // HR prev inicio
	
	// Formatação das datas de previsão
	$dtPrevIni	= ($auxDtPrevIni == "") ? "" : $auxDtPrevIni ." ". $auxHrPrevIni; // data formatada [PREV_DTT_INI]
	$dtPrevIni	= cDate(CFG_LANG,$dtPrevIni,true); 	 // data formatada [PREV_DTT_INI]
	
	// Controle de ACESSO
	// if($strPopulate  == "yes"){initModuloParams(basename(getcwd()));}//Popula o session para fazer a abertura dos ítens do módulo
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "GERA");
	
	// TRATAMENTO DE CAMPOS VAZIOS
	if($dtPrevIni	 == "") { $strMsg .= "&bull;&nbsp;Informar Data Agendamento<br>";}
	if($auxHrPrevIni == "") { $strMsg .= "&bull;&nbsp;Informar Hora inicial do Agendamento<br>"; }
	
	// Exibe mensagem de ERRO
	if($strMsg != ""){  
		mensagem("err_dados_titulo","err_dados_submit_desc",$strMsg,"STGeraHomo.php?var_chavereg=".$intCodDado, "erro", 1);
		die();
	}
		
	// Inicia objeto para manipulação do banco
	$objConn = abreDBConn(CFG_DB);
	
	// Concatena OBSERVAÇÃO COM OBSERVAÇÃO DO SISTEMA
	$strObs = "PEDIDO DE SOLICITAÇÃO DE HOMOLOGAÇÃO GERADO PARA ".$strNome." <br>(CPF: " . $strCPF . ") EM ".dDate(CFG_LANG,now(),false) . "<br>" . $strObs;
	
	// busca o produto homologação 
	// de ultima validade
	try {
		$strSQL = "
				SELECT
					 prd_produto.cod_produto
					,prd_produto.rotulo
					,prd_produto.valor
					,prd_produto.descricao
					,prd_produto.dt_ini_val_produto
					,prd_produto.dt_fim_val_produto
				FROM
					prd_produto
				WHERE
					CURRENT_DATE BETWEEN prd_produto.dt_ini_val_produto AND prd_produto.dt_fim_val_produto 
				AND	prd_produto.tipo = 'homo'
				AND	prd_produto.dtt_inativo IS NULL
				ORDER BY prd_produto.valor DESC ";
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	// Caso nao tenha localizado um produto
	// de homologação válido, exibe msg erro
	if($objResult->rowCount() <= 0){
		mensagem("err_sql_titulo","err_sql_desc",getTText("produto_homo_validade_off",C_NONE),"","erro",1);
		die();
	}
	
	// Fetch dos dados do produto válido corrente
	$objRS = $objResult->fetch();
	
	// Passagem dos dados do produto	
	$intCodProduto = (getValue($objRS,"cod_produto") 		== "") ? "" : getValue($objRS,"cod_produto");
	$strRotuloProd = (getValue($objRS,"rotulo") 			== "") ? "" : getValue($objRS,"rotulo");
	$strDescProd   = (getValue($objRS,"descricao") 			== "") ? "" : getValue($objRS,"descricao");
	$intVlrProduto = (getValue($objRS,"valor") 				== "") ? "" : getValue($objRS,"valor");
	$dtIniValidade = (getValue($objRS,"dt_ini_val_produto") == "") ? "" : getValue($objRS,"dt_ini_val_produto");
	$dtFimValidade = (getValue($objRS,"dt_fim_val_produto") == "") ? "" : getValue($objRS,"dt_fim_val_produto");
		
	// INICIA A TRANSAÇÃO
	$objConn->beginTransaction();
	try{
		// Insere o pedido de homologação
		$strSQL = "
			INSERT INTO prd_pedido(	 
				  cod_pj
				, it_cod_pf
				, it_cod_pj_pf
				, it_cod_produto
				, it_descricao
				, it_dt_ini_val_produto
				, it_dt_fim_val_produto
				, it_dtt_agendamento
				, it_tipo
				, it_valor
				, valor
				, obs
				, situacao
				, sys_dtt_ins
				, sys_usr_ins
			) VALUES ( 
				  ". $intCodPJ ."
				, ". $intCodPF ."
				, ". $intCodDado ."
				, ". $intCodProduto ."
				, '". $strRotuloProd ."'
				, '". $dtIniValidade ."'
				, '". $dtFimValidade ."'
				, '". $dtPrevIni ."'
				, 'homo'
				, ". $intVlrProduto ."
				, ". $intVlrProduto ."
				, '". $strObs ."'
				, 'aberto' 
				, CURRENT_TIMESTAMP
				, '" . getSession(CFG_SYSTEM_NAME . "_id_usuario") . "');";
		$objConn->query($strSQL);
			
		// Insere observação na relação que foi rea-
		// Lizado pedido de homologação na data atual
		$strSQL = "
			UPDATE relac_pj_pf 
			SET obs = '" . $strObs . "'
			  , sys_dtt_upd = CURRENT_TIMESTAMP
			  , sys_usr_upd = '" . getSession(CFG_SYSTEM_NAME . "_id_usuario") . "'
			WHERE cod_pj_pf = " . $intCodDado;
		$objConn->query($strSQL);
		
		// COMMIT NA TRANSAÇÃO	
		$objConn->commit();
	}catch(PDOException $e){
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
		
	$objConn = NULL;
	redirect($strRedirect);
?>
