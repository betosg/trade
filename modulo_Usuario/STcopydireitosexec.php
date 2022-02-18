<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$objConn = abreDBConn(CFG_DB);

$strLocation       = request("var_location"); // P�gina para aonde ser� redirecionado ap�s a execu��o das opera��es
$intCodUsuarioDe   = request("var_de");       // C�digo do usu�rio que servir� como base para a c�pia dos direitos
$intCodUsuarioPara = request("var_para");     // C�digo do usu�rio que receber� os direitos

($intCodUsuarioPara == "") ? $strErro = "N�o foi selecionado um usu�rio para receber os direitos" : $strErro = NULL;
($intCodUsuarioDe   == "") ? $strErro = "N�o foi selecionado um usu�rio para ser base da c�pia"   : $strErro = NULL;

if(!is_null($strErro)){
	mensagem("Aviso:",$strErro,"","javascript:history.back()","erro",1);
	die();
}

try{
	$objConn->beginTransaction();
	
	$objStatement = $objConn->prepare("SELECT sp_copia_direitos(:de, :para);");
	$objStatement->bindParam(":de",$intCodUsuarioDe);
	$objStatement->bindParam(":para",$intCodUsuarioPara);
	$objStatement->execute();
	
	$objConn->commit();
}
catch(PDOException $e){
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	$objConn->rollBack();
	die();
}

redirect("../modulo_Usuario/");
?>