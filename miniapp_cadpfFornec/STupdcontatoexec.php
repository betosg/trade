<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");

	// Abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// REQUESTS
	$strLOCATION = request("DEFAULT_LOCATION");
	
	$strCPF			= request("DBVAR_STR_CPF");
	$strNOME		= request("DBVAR_STR_CONTATO");
	$strFone		= request("DBVAR_STR_FONE");
	$strEMAIL		= request("DBVAR_STR_EMAIL");
	$strFUNCAO		= request("DBVAR_STR_CARGO");
	
	
	
	
	// REQUEST - PJ
	$intCodPJ 	 = request("DBVAR_INT_COD_PJ");
	$intCodPF	 = request("DBVAR_INT_COD_PF");
	
	
	// Inicializa a Transação para prevenção
	// de possíveis falhas e 'INSERÇÃO AOS PEDAÇOS'
	$objConn->beginTransaction();
	try{
	
			// Atualiza PF encaminhada [COLABORADOR]
		$strSQL = "UPDATE  cad_pf_fornec SET cpf = '".prepStr($strCPF)."'
										 , nome = '".prepStr($strNOME)."'
										 , endprin_fone1 = '".$strFone."'
										 , email = '".prepStr($strFone)."'
										 , sys_usr_upd = '".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."'
										 , sys_dtt_upd = CURRENT_TIMESTAMP
										WHERE cod_pf_fornec = ".$intCodPF;
			$objConn->query($strSQL);
			
			// Atualiza RELAÇÃO da Vaga do COLABORADOR para a PJ
		$strSQL = "UPDATE relac_pj_pf_fornec SET	  
											   funcao = '".prepStr($strFUNCAO)."'
		 									  , sys_usr_upd = '".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."'
											  , sys_dtt_upd = CURRENT_TIMESTAMP
   										WHERE cod_pf_fornec = ".$intCodPF;
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