<?php
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

include_once("../_database/athdbconn.php");

$strTable  = request("var_table");
$strSchema = (request("var_schema") == "") ? "public" : request("var_schema");

$objConn  = abreDBConn(CFG_DB);

try {
	$strSQL = " SELECT cols.column_name, dtd_identifier 
				  FROM information_schema.columns AS cols 
				 WHERE cols.table_schema = '" . $strSchema . "' 
				   AND cols.table_name = '" . $strTable . "' 
			  ORDER BY dtd_identifier ";
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	header("HTTP/1.0 500 Server internal error");
	echo($e->getMessage());
	die();
}

$intI = 0;

foreach($objResult as $objRS) { 
	echo((($intI != 0) ? "\n" : "") . getValue($objRS,0)); 
	$intI++;
}

$objResult->closeCursor();
?>
