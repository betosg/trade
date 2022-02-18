<?php
include_once("../_database/athdbconn.php");
include_once("../_database/athtranslate.php");

$objConn = abreDBConn(CFG_DB);

$strLocation       = request("var_location"); // Pсgina para aonde serс redirecionado apѓs a execuчуo das operaчѕes
$intCodUsuarioDe   = request("var_de");       // Cѓdigo do usuсrio que servirс como base para a cѓpia dos direitos
$intCodUsuarioPara = request("var_para");     // Cѓdigo do usuсrio que receberс os direitos

($intCodUsuarioPara == "") ? $strErro = "Nуo foi selecionado um usuсrio para receber os direitos" : $strErro = NULL;
($intCodUsuarioDe   == "") ? $strErro = "Nуo foi selecionado um usuсrio para ser base da cѓpia"   : $strErro = NULL;

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