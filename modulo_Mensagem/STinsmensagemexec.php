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
	$strUsrRemetente = request("DBVAR_STR_ID_USUARIO_REMETENTE");
	$strUsrDestinata = request("DBVAR_STR_ID_USUARIO_DESTINATARIO");
	
	$strAssunto 	 = request("DBVAR_STR_ASSUNTO");
	$strMensagem 	 = request("DBVAR_STR_MENSAGEM");
	$strDttEnvio	 = cDate(CFG_LANG,request("DBVAR_DATE_DTT_ENVIO"),true);
		
	$strUsrIns 		 = request("DBVAR_STR_SYS_USR_INS");
	$strLocation	 = request("DEFAULT_LOCATION");
		
	// Tratamento dos campos
	$strERRO  = "";
	$strERRO .= ($strUsrRemetente == "") ? "&bull;&nbsp".getTText("remet_vazio",C_NONE)."<br />"   : "";
	$strERRO .= ($strUsrDestinata == "") ? "&bull;&nbsp".getTText("dest_vazio",C_NONE)."<br />"    : "";
	$strERRO .= ($strAssunto      == "") ? "&bull;&nbsp".getTText("assunt_vazio",C_NONE)."<br />"  : "";
	$strERRO .= ($strMensagem     == "") ? "&bull;&nbsp".getTText("msg_vazio",C_NONE)."<br />"     : "";
	$strERRO .= ($strDttEnvio     == "") ? "&bull;&nbsp".getTText("dtt_env_vazio",C_NONE)."<br />" : "";
	
	if($strERRO != ""){
		mensagem("err_dados_titulo","err_dados_submit_desc",$strERRO,"","aviso",1,"","");
		die();
	}
	
	// abre objeto para manipulação com o banco
	$objConn = abreDBConn(CFG_DB);
		
	// Faz inserção de mensagem no banco e
	// destinatário + remetente nas tabelas
	try{
		// Inicializa a transação
		$objConn->beginTransaction();
		
		// Insere Mensagem
		$strSQL = "INSERT INTO msg_mensagem(assunto, mensagem, dtt_envio, cod_msg_pasta) 
				   VALUES('".prepStr($strAssunto)."','".prepStr($strMensagem)."','".prepStr($strDttEnvio)."', 1)";
		$objConn->query($strSQL);
		
		// Busca currval da mensagem
		$objResult = $objConn->query("SELECT currval('msg_mensagem_cod_mensagem_seq') AS cod_mensagem;");
		$objRS	   = $objResult->fetch();
		$intCodMensagem = getValue($objRS,"cod_mensagem");
		
		// Insere nas tabelas de destinarário e Remetente
		$strSQL = "INSERT INTO msg_destinatario(cod_mensagem,id_usuario_destinatario)
				   VALUES(".$intCodMensagem.",'".prepStr($strUsrDestinata)."')";
		$objConn->query($strSQL);
		$strSQL = "INSERT INTO msg_remetente(cod_mensagem,id_usuario_remetente)
				   VALUES(".$intCodMensagem.",'".prepStr($strUsrRemetente)."')";
		$objConn->query($strSQL);
		
		$objConn->commit();
	}catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		$objConn->rollBack();
		die();
	}
	
	// redirect da página após inserção
	// se comporta corretamente mesmo
	// em OK ou APLICAR
	if($strLocation != "") { redirect($strLocation); }
	else { echo("<script type='text/javascript' language='javascript'>window.close();</script>"); }
?>