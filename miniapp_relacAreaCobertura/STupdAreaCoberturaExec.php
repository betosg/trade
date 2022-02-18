<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");

	// Abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// REQUESTS
	$strLOCATION = request("DEFAULT_LOCATION");
	
	
	
	
	
	
	// REQUEST - PJ
	$intCodAreaCobertura 	 = request("dbvar_codigo_area_cobertura");
	$strMunicipio            = request("cod_municipio");
	$intCodPJ                = request("codigo_pai");
	 $strCoordGps               = request("DBVAR_STR_COORD_GPS");
	
	// Inicializa a Transação para prevenção
	// de possíveis falhas e 'INSERÇÃO AOS PEDAÇOS'
	$objConn->beginTransaction();
	try{
	
			// Atualiza PF encaminhada [COLABORADOR]
				echo	$strSQL = "UPDATE relac_area_cobertura SET cod_municipio = " .$strMunicipio.
													  ", sys_usr_upd = '".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario")) ."'".
													  ", sys_dtt_upd = CURRENT_TIMESTAMP ".
													  ", coord_gps = '".prepStr($strCoordGps) ."'".
						"WHERE cod_area_cobertura = ". $intCodAreaCobertura;																	
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