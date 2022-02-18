<?php
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

include_once("../_database/athdbconn.php");

$strSQL = request("var_sql");

//substituimos as variaveis de ambiente contidas no SQL pelo seu valor - By Vini 20.03.13
$strSQL = replaceParametersSession($strSQL);

$objConn  = abreDBConn(CFG_DB);

try {
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	header("HTTP/1.0 500 Server internal error");
	echo($e->getMessage());
	die();
}

$intI = 0;

foreach($objResult as $objRS) {
	echo((($intI != 0) ? "\n" : "") . getValue($objRS,0,false) . "|" . getValue($objRS,1,false));
	$intI++;
}

$objResult->closeCursor();
?>
