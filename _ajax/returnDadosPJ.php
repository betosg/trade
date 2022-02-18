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
    echo((($intI != 0) ? "\n" : "") . getValue($objRS,0) . "|" . getValue($objRS,1) . "|" . getValue($objRS,2) . "|" . getValue($objRS,3) . "|" . getValue($objRS,4) . "|" . getValue($objRS,5) . "|" . getValue($objRS,6) . "|" . getValue($objRS,7) . "|" . getValue($objRS,8) . "|" . getValue($objRS,9) . "|" . getValue($objRS,10) . "|" . getValue($objRS,11) . "|" . getValue($objRS,12) . "|" . getValue($objRS,13) . "|" . getValue($objRS,14) . "|" . getValue($objRS,15) . "|" . getValue($objRS,16));
    $intI++;
}

$objResult->closeCursor();
?>
