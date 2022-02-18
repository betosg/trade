<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");

	// Abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// REQUESTS
	$strLOCATION = request("DEFAULT_LOCATION");
	
	
	
	
	
	
	// REQUEST - PJ
	$intCodPfAtuacao		 = request("dbvar_cod_atuacao");	
	$intCodPJ            = request("codigo_pai");
	$strAtuacao            = request("DBVAR_STR_ATUACAO");
	
	// Inicializa a Transação para prevenção
	// de possíveis falhas e 'INSERÇÃO AOS PEDAÇOS'
	$objConn->beginTransaction();
	try{
	
			// Atualiza PF encaminhada [COLABORADOR]
					$strSQL = "UPDATE cad_pf_atuacao SET cod_atuacao = " .$strAtuacao.													  													  
						" WHERE cod_pf_atuacao = ". $intCodPfAtuacao;
			$objConn->query($strSQL);
			
			
// Commit na TRANSAÇÃO
		$objConn->commit();
	}catch(PDOException $e){
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	//die();
	redirect("index.php?var_chavereg=".$intCodPJ);
	
?>