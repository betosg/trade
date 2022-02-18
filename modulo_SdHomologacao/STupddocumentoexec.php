<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	
	// REQUESTS
	$intCodDado   = request("var_chavereg");	 // COD_HOMOLOGACAO_DOC
	$strTIPO	  = request("var_tipo"); 		 // TIPO DOCUMENTO
	$strTitulo	  = request("var_titulo"); 		 // TITULO DOCUMENTO
	$strHtmlTexto = request("var_html_texto");	 // TEXTO HTML
	$dtINATIVO	  = request("var_dtt_inativo");  // DATA_INATIVO
	$strLOCATION  = request("DEFAULT_LOCATION"); // REDIRECT
	
	$dtINATIVO = ($dtINATIVO == "") ? 'NULL' : "'".cDate(CFG_LANG,$dtINATIVO,true)."'";
	
	// CARREGA PREFIX PARA SESSION
	// $strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));          
	// VERIFICAÇÃO DE ACESSO
	// verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"),"INS_RESP");
		
	if($intCodDado == ""){
		mensagem("err_sql_desc_card","err_envio_ag",getTText("cod_homo_null",C_NONE),"","erro","1");
		die();
	}

	// ABERTURA DE CONEXÃO COM DB
	$objConn = abreDBConn(CFG_DB);
	
	// TRANSAÇÃO PARA INSERIR DOCUMENTO DE HOMOLOGAÇÃO
	$objConn->beginTransaction();
	try{
		$strSQL = "UPDATE sd_homologacao_documento SET tipo='".prepStr($strTIPO)."',titulo='".prepStr($strTitulo)."',html_texto='".prepStr($strHtmlTexto)."',sys_usr_upd='".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."',sys_dtt_upd=CURRENT_TIMESTAMP,dtt_inativo=".$dtINATIVO." WHERE cod_homologacao_documento = ".$intCodDado;
		$objConn->query($strSQL);
		$objConn->commit();		
	}catch(PDOException $e) {
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
?>
<script type="text/javascript" language="javascript">window.location.href="<?php echo($strLOCATION);?>";</script>