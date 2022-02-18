<?php
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

include_once("../_database/athdbconn.php");

$intCodigo = request("var_codigo");
$strTipo = request("var_tipo");

if (($intCodigo != "") && ($strTipo != "")) {
	$objConn = abreDBConn(CFG_DB);
	
	try {
		$strSQL  = "SELECT titulo, descricao, dt_ini, dt_fim
					  FROM cad_evento_entidade
					 WHERE codigo = " . $intCodigo . "
					   AND tipo = '" . $strTipo . "' ";
		$objResult = $objConn->query($strSQL);
	} catch(PDOException $e) {
		header("HTTP/1.0 500 Server internal error");
		echo($e->getMessage());
		die();
	}
	
	foreach($objResult as $objRS){ 
		echo getValue($objRS,"titulo") . "|" . getValue($objRS,"descricao") . "|" . getValue($objRS,"dt_ini") . "|" . getValue($objRS,"dt_fim") . "<br>";
	}
	
	$objResult->closeCursor();
	$objConn = NULL;
}
?>