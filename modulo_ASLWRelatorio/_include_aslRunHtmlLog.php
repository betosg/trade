<?php 
/* INI: Grava Log de execuзгo do relatуrio ----------------------------------------------------- */
	$objConn->beginTransaction();
	try{
		// USANDO MKTIME (resposta 00:54) * USANDO MICROTIME (resposta era em float e apresentava pouca relevвncia: 54,344)
		$data_fim = strtotime(date("Y-m-d H:i:s"));
		$scusto   = mktime(date('H', $data_fim) - date('H', $data_ini), date('i', $data_fim) - date('i', $data_ini), (date('s', $data_fim)+1) - date('s', $data_ini));
		$scusto   = date('i:s',$scusto);
		
		$strSQL  = " INSERT INTO aslw_relatorio_log (cod_relatorio, nome,  inputs, arquivo, sys_usr_ins, custo_seg) ";
		$strSQL .= " VALUES (".$intRelCod.",'".getNormalString($strRelTit)."','".$strRelInpts."','".$arqNome."','".getsession(CFG_SYSTEM_NAME."_id_usuario")."','".$scusto."')";
		$objConn->query($strSQL);
		$objConn->commit();
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollBack();
		die();
	}
/* FIM: Grava Log de execuзгo do relatуrio ----------------------------------------------------- */
?>