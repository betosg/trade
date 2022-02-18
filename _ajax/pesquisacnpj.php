<?php
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

ini_set("error_reporting","E_ERROR & ~E_WARNING & ~E_NOTICE");

include_once("../_database/athdbconn.php");

$strCodPJ = request("var_pj");

$objConn = abreDBConn(CFG_DB);

$strValor = "";
$strSQL = " SELECT
			  doc.valor
		 FROM 
			  cad_doc_pj AS doc
		WHERE doc.nome = 'CNPJ' AND doc.cod_pj = " . $strCodPJ ;
$objResult = $objConn->query($strSQL);
$objRS = $objResult->fetch();
$strValor = getValue($objRS,"valor");

echo($strValor);

$objResult->closeCursor();
?>