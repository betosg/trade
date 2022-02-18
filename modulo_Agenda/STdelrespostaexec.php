<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");

	// verificação de ACESSO
	// carrega o prefixo das sessions
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// verificação de acesso do usuário corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"DEL");

	// REQUESTS
	$intCodResposta	= request("var_chavereg");		// cod_agenda para o qual resposta irá ser relacionada
	$strRedirect	= request("var_redirect");		// pagina que sera feito o redir
		
	// cod_agenda nao pode vir vazio
	if($intCodResposta == ""){
		mensagem("err_sql_desc_card","err_envio_ag",getTText("agenda_cod_null",C_NONE),$strRedirect,'erro','1');
		die();
	}
	
	// abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
		
	// faz inserção de resposta no DB e redirect
	// para página que lista RESPOSTAS TABLESORT
	try{
		$strSQL = "DELETE FROM ag_resposta WHERE cod_resposta = ".$intCodResposta;
		$objConn->query($strSQL);
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),$strRedirect,"erro",1);
		die();
	}
	
	// close da página
	if($strRedirect == ""){
		echo("<script type='text/javascript'>window.close();</script>");
	}else{
		redirect($strRedirect);
	}
?>