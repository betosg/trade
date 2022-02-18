<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");

	// Abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// REQUESTS
	$strLOCATION = request("DEFAULT_LOCATION");
	
	$strCPF			= request("DBVAR_STR_CPF");
	$strNOME			= request("DBVAR_STR_CONTATO");
	$strFone			= request("DBVAR_STR_FONE");
	$strEMAIL		= request("DBVAR_STR_EMAIL");
	$strFUNCAO		= request("DBVAR_STR_CARGO");
	
	
	
	
	// REQUEST - PJ
	$intCodPJ 	 = request("DBVAR_INT_COD_PAI");
	
	
	
	// Inicializa a Transação para prevenção
	// de possíveis falhas e 'INSERÇÃO AOS PEDAÇOS'
	$objConn->beginTransaction();
	try{
		// PARA NOVOS COLABORADORES, CAD_PF + RELAÇÃO
		
			// Insere PF encaminhada [COLABORADOR]
		echo "<br><br>".	$strSQL = "INSERT INTO cad_pf_fornec (cpf										 
										 , nome
										 , endprin_fone1
										 , email
										 , sys_usr_ins
										 , sys_dtt_ins) 
						VALUES ('".prepStr($strCPF)."','"
								  .prepStr($strNOME)."','"
								  .prepStr($strFone)."','"
								  .prepStr($strEMAIL)."','"
								  .prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))
								  ."',CURRENT_TIMESTAMP);";
			$objConn->query($strSQL);
			
			// Localiza esta PF recém-inserida
			$objResult = $objConn->query("  SELECT last_value AS max_cod_pf FROM cad_pf_fornec_cod_pf_fornec_seq;");
			$objRS	   = $objResult->fetch();
			$intCodPF  = getValue($objRS,"max_cod_pf");
			
			// Insere RELAÇÃO da Vaga do COLABORADOR para a PJ
		echo "<br><br>".	$strSQL = "INSERT INTO relac_pj_pf_fornec (cod_pj_fornec
											  , cod_pf_fornec			  
											  , funcao
											  , sys_usr_ins
											  , sys_dtt_ins) 
								VALUES (".$intCodPJ.","
										.$intCodPF.",'"										
										.prepStr($strFUNCAO)."','"										
										.prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))
										."',CURRENT_TIMESTAMP);";
			$objConn->query($strSQL);
			
			
// Commit na TRANSAÇÃO
		$objConn->commit();
	}catch(PDOException $e){
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	redirect("index.php?var_chavereg=".$intCodPJ);
	
?>