<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");

	// Abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// REQUESTS
	$strLOCATION = request("DEFAULT_LOCATION");
	
	
	
	$strMarca	= request("DBVAR_STR_MARCA");
	
	
	
	// REQUEST - PJ
	$intCodPJ 	 = request("DBVAR_INT_COD_PAI");
	
	
	
	// Inicializa a Transação para prevenção
	// de possíveis falhas e 'INSERÇÃO AOS PEDAÇOS'
	$objConn->beginTransaction();
	try{
		
	
		echo	$strSQL = "INSERT INTO cad_pj_marcas (cod_pj
											  , marca
											  , sys_usr_ins
											  , sys_dtt_ins
											  ) 
								VALUES (".$intCodPJ.",'"
										.$strMarca."','"																														
										.prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))
										."',CURRENT_TIMESTAMP )";
										
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