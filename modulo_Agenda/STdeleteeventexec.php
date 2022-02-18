<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");

	// verificaзгo de ACESSO
	// carrega o prefixo das sessions
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	
	// verificaзгo de acesso do usuбrio corrente
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"DEL");

	// ligando para true, a verificaзгo de usuбrio й feita,
	// para caso o usuario criador seja diferente do usuario
	// atual da sessгo, entгo mostrar mensagem de erro [isso
	// para os casos onde a agenda possui mais de uma resposta
	// OBS: QUANDO LIGAR TRUE, ALTERAR PARA CASCADE NO BANCO
	$boolOnVerify   = false;
	
	// abre objeto para manipulaзгo com o banco
	$objConn = abreDBConn(CFG_DB);

	// REQUESTS
	$intCodAgenda	= request("var_chavereg");		// cod_agenda para o qual irб ser update da agenda
	$strRedirect	= request("var_redirect");		// pagina que sera feito o redir
	$strUsrCriador  = request("var_responsavel");   // id do usuario criador / responsavel da agenda
	$intQntResposta = request("var_qnt_resposta");  // quantidade de respostas, maior que zero, sу DEL se usr sessгo = $strUsrCr
	
	// cod_agenda nao pode vir vazio
	if($intCodAgenda == ""){
		mensagem("err_sql_desc_card","err_envio_ag",getTText("agenda_cod_null",C_NONE),'STdeleteevent.php?var_chavereg='.$intCodAgenda."&var_redirect=".$strRedirect,'erro','1');
		die();
	}
		
	// verifica se o usuario da sessao й o criador
	// da agenda, para liberar a exclusгo. somente
	// o criador pode deletar a agenda, nos casos
	// de a agenda possuir resposta.
	if(($intQntResposta > 0) && ($boolOnVerify)){
		if($strUsrCriador != getsession(CFG_SYSTEM_NAME."_id_usuario")){
			mensagem("err_sql_titulo","err_sql_desc",getTText("erro_usr_diferente"),'STdeleteevent.php?var_chavereg='.$intCodAgenda."&var_redirect=".$strRedirect,"erro",1);
			die();
		}
	}else{
		try{
			$strSQL = "DELETE FROM ag_agenda WHERE cod_agenda = ".$intCodAgenda;
			$objConn->query($strSQL);
		}catch(PDOException $e){
			mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),'STdeleteevent.php?var_chavereg='.$intCodAgenda."&var_redirect=".$strRedirect,"erro",1);
			die();
		}
	}
	// close da pбgina
	redirect($strRedirect);
?>