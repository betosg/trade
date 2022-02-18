<?php 
/* INI: Apaga os arquivos do relatório corrente que tenham mais de XX dias --------------------- */

	//Remove as os arquivos fisicamente apartir da lista do log	
	try{
		$strSQL  = " SELECT cod_relatorio_log, arquivo ";
		$strSQL .= "  FROM aslw_relatorio_log ";
		$strSQL .= " WHERE sys_dtt_ins < (CURRENT_DATE - '30 days'::interval) ";
		$strSQL .= "   AND cod_relatorio = " . $intRelCod;
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollBack();
	}

	$myDir  = realpath("../../" . $dirCli . "/asl_html/") . "/";
	$objConn->beginTransaction();
	foreach($objResult as $objRS) {
		$myFile = $myDir . getValue($objRS,"arquivo");
		//Remove o arquivo fisicamente	
		if (file_exists ($myFile)) { unlink($myFile); }
		//Remove o registro na tabela de logo
		try{
			$strSQL  = "DELETE FROM aslw_relatorio_log WHERE cod_relatorio_log = " .getValue($objRS,"cod_relatorio_log");
			$objConn->query($strSQL);
		}
		catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
			$objConn->rollBack();
			die();
		}
	}
	$objConn->commit();

/* FIM: Apaga os arquivos do relatório corrente que tenham mais de XX dias --------------------- */
?>
