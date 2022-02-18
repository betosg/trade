<?php
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

include_once("../_database/athdbconn.php");

$objConn = abreDBConn(CFG_DB);

try {
	$strSQL  = "SELECT cod_pj, nome_fantasia
				FROM cad_pj
				WHERE dtt_inativo IS NULL 
				AND nome_fantasia IS NOT NULL 
				AND nome_fantasia <> ''
				ORDER BY nome_fantasia ";
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	header("HTTP/1.0 500 Server internal error");
	echo($e->getMessage());
	die();
}

foreach($objResult as $objRS){ 
	echo getValue($objRS,"cod_pj") . "|" . getValue($objRS,"nome_fantasia") . "<br>";
}

$objResult->closeCursor();
$objConn = NULL;
?>