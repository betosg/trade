<?php
 include_once("../_database/athdbconn.php");

 $strSesPfx  = strtolower(str_replace("modulo_","",basename(getcwd())));
 verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "INS");

 $strGrpUser = request("var_grupo");    	// Identificaчуo do Grupo serve neste caso apenas para o redirect do final voltar pra o estado correto da pсgina anterior
 $intCodAba  = request("var_chavereg_pai"); // Identificaчуo da ABA pai do container a ser criado

 $objConn = abreDBConn(CFG_DB);
 try{
	$objConn->beginTransaction();

	$strSql  = "INSERT INTO sys_mx_item (cod_mx, rotulo, ordem ) VALUES (" . $intCodAba . ", 'novo_container',0)";
	$objConn->query($strSql);
	$objConn->commit();
 }
 catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",0);
	$objConn->rollBack();
	die();
 }
 redirect("STsetmenumx.php?var_strparam=" . $strGrpUser);
?>