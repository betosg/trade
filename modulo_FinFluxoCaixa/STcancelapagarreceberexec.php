<?php
	include_once("../_database/athdbconn.php");

	$strSesPfx = strtolower(str_replace("modulo_","",basename(getcwd())));
	verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "CANCEL");
	
	$intCodDado = request("var_chavereg");
	
	$objConn = abreDBConn(CFG_DB);
	
	try{
		$strSQL  = " UPDATE fin_conta_pagar_receber ";
		$strSQL .= " SET situacao = 'cancelado' ";
		$strSQL .= "   , sys_dtt_cancel = CURRENT_TIMESTAMP ";
		$strSQL .= "   , sys_usr_cancel = '" . getsession(CFG_SYSTEM_NAME . "_id_usuario") . "' ";
		$strSQL .= " WHERE cod_conta_pagar_receber = " . $intCodDado;
		
		$objResult = $objConn->query($strSQL);
	}
	catch(PDOException $e){
		mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
		die();
	}
	redirect(getsession($strSesPfx . "_grid_default"));
?>