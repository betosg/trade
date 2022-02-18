<?php
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

include_once("../_database/athdbconn.php");

$strSQL 		= request("var_sql");
$strDBConnect 	= request("var_db");

$objConn  = abreDBConn($strDBConnect);

try {
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	header("HTTP/1.0 500 Server internal error");
	echo($e->getMessage());
	die();
}

$intI = 0;

foreach($objResult as $objRS) {
	echo((($intI != 0) ? "\n" : "") . getValue($objRS,0) . "|" . getValue($objRS,1));
	$intI++;
}

$objResult->closeCursor();
?>
