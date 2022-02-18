<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");

	// Abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// REQUESTS
	$strLOCATION = request("DEFAULT_LOCATION");
	
	
	
	$strArea	= request("DBVAR_STR_ATUACAO");
	
	
	
	// REQUEST - PJ
	$intCodPJ 	 = request("DBVAR_INT_COD_PAI");
	
	
	
	// Inicializa a Transação para prevenção
	// de possíveis falhas e 'INSERÇÃO AOS PEDAÇOS'
	$objConn->beginTransaction();
	try{
		
	
		echo	$strSQL = "INSERT INTO cad_pf_atuacao (cod_pf
											  , cod_atuacao
											  
											  ) 
								VALUES (".$intCodPJ.",'"
										.$strArea."')";
										
			$objConn->query($strSQL);

			
			
			
// Commit na transa��o
		$objConn->commit();
	}catch(PDOException $e){
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	
	redirect("index.php?var_chavereg=".$intCodPJ);
	
?>