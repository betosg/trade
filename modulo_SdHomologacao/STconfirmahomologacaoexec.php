<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	
	// REQUESTS
	$intCodDado  = request("var_chavereg");
	$dtHOMO      = request("var_dt_homologacao");
	$hrHOMO		 = (substr(request("var_hr_homologacao"),0,2) >= 24) ? "00". substr(request("var_hr_homologacao"),2,4).":00" : request("var_hr_homologacao"); // hr prev inicio
	$dtDEMISSAO  = request("var_dt_demissao");
	$intCodPJPF  = request("var_cod_pj_pf");
	$intCodPF    = request("var_cod_pf");
	$strOBS		 = request("var_obs");
	$strREDIRECT = request("DEFAULT_LOCATION");
	
	// CONTROLE DE ACESSO
	// if($strPopulate == "yes") { initModuloParams(basename(getcwd())); } //Popula o session para fazer a abertura dos �tens do m�dulo
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"));
	
	// Controle para DATA DE HOMOLOGACAO
	$dtHOMO = $dtHOMO." ".$hrHOMO;
	
	// ABERTURA DE CONEX�O COM DB
	$objConn = abreDBConn(CFG_DB);
	
	// TRANSA��O
	$objConn->beginTransaction();
	try{
		// MARCA A DATA DE HOMOLOGA��O
		// MARCA��O DA AGENDA � VIA TRIGGER
		$strSQL  = "
			UPDATE sd_homologacao SET 
				  dtt_homologacao = '".cDate(CFG_LANG,$dtHOMO,true)."'
				, usr_homologacao = '".getSession(CFG_SYSTEM_NAME."_id_usuario")."'
				, sys_dtt_upd = CURRENT_TIMESTAMP
				, sys_usr_upd = '".getSession(CFG_SYSTEM_NAME."_id_usuario")."'
				, obs = '".$strOBS."'
				, situacao = 'confirmado'
			WHERE cod_homologacao = ".$intCodDado;
		$objConn->query($strSQL);
		
		// MARCA��O DA DATA DE DEMISS�O
		$strSQL  = "
			UPDATE relac_pj_pf SET 
				  dt_demissao = '".cDate(CFG_LANG,$dtDEMISSAO,true)."'
				, sys_dtt_upd = CURRENT_TIMESTAMP 
				, sys_usr_upd = '".getSession(CFG_SYSTEM_NAME."_id_usuario")."' 
			WHERE cod_pj_pf = ".$intCodPJPF;
		$objConn->query($strSQL);
		
		// VERIFICA SE PF EST� RELACIONADA EM MAIS DE UMA EMPRESA
		// CASO N�O, ENT�O MARCA A PF COMO INATIVA. OBS: PF TEM DE
		// ESTAR COM DT_DEMISSAO = NULL
		$strSQL    = "SELECT dt_demissao FROM relac_pj_pf WHERE cod_pf = ".$intCodPF." AND dt_demissao IS NULL";
		$objResult = $objConn->query($strSQL);
		if($objResult->rowCount() <= 0){
			$strSQL = "
				UPDATE cad_pf SET 
					  dtt_inativo = CURRENT_TIMESTAMP
					, sys_usr_upd = '".getsession(CFG_SYSTEM_NAME."_id_usuario")."' 
					, sys_dtt_upd = CURRENT_TIMESTAMP
				WHERE cod_pf = ".$intCodPF;
			$objConn->query($strSQL);
		}
		
		// INATIVA CREDENCIAIS PARA ESTA RELA��O
		$strSQL  = "
			UPDATE sd_credencial SET 
				  sys_dtt_upd = CURRENT_TIMESTAMP
				, dtt_inativo = CURRENT_TIMESTAMP
				, sys_usr_upd = '".getSession(CFG_SYSTEM_NAME."_id_usuario")."'
			WHERE cod_pj_pf = ".$intCodPJPF;
		$objConn->query($strSQL);
		
		// COMMIT NA TRANSA��O
		$objConn->commit();
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollback();
		die();
	}
	
	// DESTR�I OBJETO DE CONEX�O COM DB
	$objConn = NULL;
	
	redirect($strREDIRECT);
?>