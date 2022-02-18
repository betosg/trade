<?php
	// INCLUDES
	include_once("../_database/athdbconn.php");
	include_once("../_database/athtranslate.php");
	include_once("../_database/athkernelfunc.php");
	
	// REQUESTS
	$intCodDado   = request("var_chavereg");	 // COD_HOMOLOGACAO
	$strTIPO	  = request("var_tipo"); 		 // TIPO DOCUMENTO
	$strTitulo	  = request("var_titulo"); 		 // TITULO DOCUMENTO
	$strHtmlTexto = request("var_html_texto");	 // TEXTO HTML
	$strFlagABRIR = request("var_flag_abrir"); 	 // FLAG PARA ABRIR POPUP DOCUMENTO
	$strLOCATION  = request("DEFAULT_LOCATION"); // REDIRECT
	
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
		$strSQL = "INSERT INTO sd_homologacao_documento(cod_homologacao,tipo,titulo,html_texto,sys_usr_ins,sys_dtt_ins) VALUES (".$intCodDado.",'".prepStr($strTIPO)."','".prepStr($strTitulo)."','".prepStr($strHtmlTexto)."','".prepStr(getsession(CFG_SYSTEM_NAME."_id_usuario"))."',CURRENT_TIMESTAMP)";
		$objConn->query($strSQL);
		
		$strSQL = "SELECT MAX(sd_homologacao_documento.cod_homologacao_documento) AS codigo FROM sd_homologacao_documento";
		$objResult = $objConn->query($strSQL);
		$objRS  = $objResult->fetch();
		$intCodDOC = getValue($objRS,"codigo");
		
		$objConn->commit();		
	}catch(PDOException $e) {
		$objConn->rollBack();
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1,"");
		die();
	}
?>
<script type="text/javascript" language="javascript">
	<?php if($strFlagABRIR != ""){?>
	window.open('STimprdocumento.php?var_chavereg=<?php echo($intCodDOC);?>','','width=700,height=600');
	<?php }?>
	window.location.href="<?php echo($strLOCATION);?>";
</script>