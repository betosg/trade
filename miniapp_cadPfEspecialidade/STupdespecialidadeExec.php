<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");

	// Abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// REQUESTS
	$strLOCATION = request("DEFAULT_LOCATION");
	
	
	
	
	
	
	// REQUEST - PJ
	$intCodPfespecialidade		 = request("dbvar_cod_especialidade");	
	$intCodPJ                = request("codigo_pai");
	$strespecialidade              = request("DBVAR_STR_especialidade");
	
	// Inicializa a Transação para prevenção
	// de possíveis falhas e 'INSERÇÃO AOS PEDAÇOS'
	$objConn->beginTransaction();
	try{
	
			// Atualiza PF encaminhada [COLABORADOR]
					$strSQL = "UPDATE cad_pf_especialidade SET cod_especialidade = " .$strespecialidade.													  													  
						" WHERE cod_pf_especialidade = ". $intCodPfespecialidade;
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