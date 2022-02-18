<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");

	// Abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// REQUESTS
	$strLOCATION = request("DEFAULT_LOCATION");
	
	
	
	
	
	
	// REQUEST - PJ
	$intCodMarca 		 = request("dbvar_codigo_marca");	
	$intCodPJ            = request("codigo_pai");
	$strMarca            = request("DBVAR_STR_MARCA");
	
	// Inicializa a Transação para prevenção
	// de possíveis falhas e 'INSERÇÃO AOS PEDAÇOS'
	$objConn->beginTransaction();
	try{
	
			// Atualiza PF encaminhada [COLABORADOR]
					$strSQL = "UPDATE cad_pj_marcas SET marca = '" .$strMarca.
													  "', sys_usr_upd = '".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario")) ."'".
													  ", sys_dtt_upd = CURRENT_TIMESTAMP ".													  
						"WHERE cod_marca = ". $intCodMarca;
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