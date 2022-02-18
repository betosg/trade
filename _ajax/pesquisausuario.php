<?php
header("Content-Type:text/html; charset=iso-8859-1");
include_once("../_database/athdbconn.php");
$objConn  = abreDBConn(CFG_DB);

$strUsuario = request("var_chavereg");
$strSQL = "SELECT id_usuario FROM sys_usuario WHERE id_usuario = '". $strUsuario ."'";

try {
	$objResult = $objConn->query($strSQL);
}catch(PDOException $e){ 	
	mensagem("err_sql_titulo","err_sql_desc",$e->getMessage(),"","erro",1);
	die();
}

//echo getValue($objResult,'id_usuario');

foreach($objResult as $objRS) { 
	echo(getValue($objRS,0)); 
}

$objResult->closeCursor();
?>
