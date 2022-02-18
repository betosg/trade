<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");

	// Abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// REQUESTS
	$strLOCATION = request("DEFAULT_LOCATION");
	
	
	$strNOME		= request("DBVAR_STR_CONTATO");
	$strMunicipio	= request("cod_municipio");
	$strEMAIL		= request("DBVAR_STR_EMAIL");
	$strFUNCAO		= request("DBVAR_STR_CARGO");
	$strCoordGps	= request("DBVAR_STR_COORD_GPS");
	
	
	
	// REQUEST - PJ
	$intCodPJ 	 = request("DBVAR_INT_COD_PAI");
	
	
	
	// Inicializa a Transação para prevenção
	// de possíveis falhas e 'INSERÇÃO AOS PEDAÇOS'
	$objConn->beginTransaction();
	try{
		
	
			$strSQL = "INSERT INTO relac_area_cobertura (cod_pj
											  , cod_municipio			  											  
											  , sys_usr_ins
											  , sys_dtt_ins
											  , coord_gps) 
								VALUES (".$intCodPJ.","
										.$strMunicipio.",'"																														
										.prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))
										."',CURRENT_TIMESTAMP "
										.",'".$strCoordGps."');";
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