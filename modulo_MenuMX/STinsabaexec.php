<?php
 include_once("../_database/athdbconn.php");

 $strSesPfx  = strtolower(str_replace("modulo_","",basename(getcwd())));
 verficarAcesso(getsession(CFG_SYSTEM_NAME . "_cod_usuario"), getsession($strSesPfx . "_chave_app"), "INS");

 $strGrpUser = request("var_grupo");    // Identificaчуo do Grupo ao qual a aba deve ser inserida

 $objConn = abreDBConn(CFG_DB);
 try{
	$objConn->beginTransaction();

	$strSql  = "INSERT INTO sys_mx (rotulo, ordem, grp_user) VALUES ('nova_aba',0,'" . $strGrpUser  . "')";
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