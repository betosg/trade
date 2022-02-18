<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athkernelfunc.php");
	
	// REQUESTS
	$intCodMensagem	= request("var_chavereg");	// cod_mensagem
	$strRedirect    = request("var_redirect");
		
	// abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
	
	// Faz update na tabela de mensagem
	// setando que ja foi lida a MSG
	try{
		$strSQL = "UPDATE msg_destino SET dtt_lido = CURRENT_TIMESTAMP WHERE cod_mensagem = ".$intCodMensagem;
		$objConn->query($strSQL);
	}catch(PDOException $e) {
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
	
	$objConn = NULL;
	
	if($strRedirect != ""){ redirect($strRedirect); }
	else{ redirect("../modulo_Mensagem/STviewmensagem.php?var_chavereg=".$intCodMensagem); }
		
?>