<?php
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	
	/*** INICIALIZA CONEXAO COM DB ***/
	$objConn = abreDBConn(CFG_DB);
	
	/*** RECEBE PARAMETROS ***/
	$intCodPJ = request("var_cod_pj");
	$strCPF   = request("var_cpf");
	$strTIPO  = request("var_tp_ins");
	
	/*** TESTA OS CAMPOS OBRIGATÓRIOS ***/
	$strMsg = "";
	
	if($intCodPJ == "") { $strMsg .= "Informar Empresa<br>"; }
	if($strCPF   == "") { $strMsg .= "Informar CPF<br>";     }
	
	if($strMsg != ""){  
		mensagem("err_dados_titulo", "err_dados_submit_desc", $strMsg, "STinsColabPasso1.php", "erro", 1);
		die();
	}
	
	//-------------------------------------------------------------------------
	// Verifica se PF do CPF informado está no sistema e se está DISPONÍVEL
	//-------------------------------------------------------------------------
	$intCodPF 		 = '';
	$bPFIndisponivel = false;
	$bPFInativada 	 = false;
	$objConn->beginTransaction();
	try{
		$strSQL = " SELECT cod_pf, nome, dtt_inativo FROM cad_pf WHERE cpf ILIKE '" . $strCPF . "' ";
		$objResult = $objConn->query($strSQL);
		
		if ($objResult->rowCount() > 0) {
			$objRS = $objResult->fetch();
			$intCodPF = getValue($objRS, "cod_pf");
			if (getValue($objRS, "dtt_inativo") != '') {
				$bPFInativada = true;
			}
		}
		$objResult->closeCursor();
		
		if (($intCodPF != '') && (!$bPFInativada)) {
			$strSQL  = " SELECT cod_pj FROM relac_pj_pf WHERE cod_pf = " . $intCodPF;
			$strSQL .= " AND dt_demissao IS NULL ";
			$objResult = $objConn->query($strSQL);
			
			if ($objResult->rowCount() > 0) {
				$bPFIndisponivel = true;
			}
			$objResult->closeCursor();
		}
		
		// Verifica também se a PF Correspondente já possui
		// VÍNCULO ATIVO COM A EMPRESA SELECIONADA
		if($intCodPF != ""){
			$strSQL  = " SELECT cod_pf FROM relac_pj_pf WHERE cod_pf = ".$intCodPF." AND cod_pj = ".getsession(CFG_SYSTEM_NAME . "_pj_selec_codigo");
			$strSQL .= " AND dt_demissao IS NULL ";
			$objResult = $objConn->query($strSQL);
			// Caso o Colaborador já tenha uma funçao
			// na empresa corrente, então tranca para
			// nao cadastrar novamente
			if($objResult->rowCount() > 0){
				mensagem("err_colab_cadastrado","err_colab_cadastrado_desc",getTText("err_colab_cadastro_desc_desc",C_NONE),"STinsColabPasso1.php?var_cod_pj=".$intCodPJ,"aviso",1);
				die();
			}
			$objResult->closeCursor();
		}
		
		$objConn->commit();
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollBack();
		die();
	}
	
	// Fecha CONEXÃO com DB
	$objConn = NULL;
	
	switch(strtoupper($strTIPO)){
		case "NEW_COLABORADOR":
			redirect("STinsColabPasso2.php?var_cod_pj=". $intCodPJ ."&var_cod_pf=". $intCodPF ."&var_cpf=". $strCPF);
		break;
		
		case "NEW_HOMOLOGACAO":
			redirect("STinshomopasso2.php?var_cod_pj=". $intCodPJ ."&var_cod_pf=". $intCodPF ."&var_cpf=". $strCPF);
		break;
			
		default:
			redirect("STinsColabPasso2.php?var_cod_pj=". $intCodPJ ."&var_cod_pf=". $intCodPF ."&var_cpf=". $strCPF);
		break;
	}
	// Mensagens de erro/alerta poderão ser revisadas depois para ficarem mais claras
	/*
	if ($bPFInativada) {
		mensagem("err_colab_inativo_titulo","err_colab_inativo_desc","","STinsColabPasso1.php?var_cod_pj=" . $intCodPJ,"aviso",1);
	}
	elseif ($bPFIndisponivel) {
		mensagem("err_colab_indisponivel_titulo","err_colab_indisponivel_desc","","STinsColabPasso1.php?var_cod_pj=" . $intCodPJ,"aviso",1);
	}
	else {}
	*/
?>