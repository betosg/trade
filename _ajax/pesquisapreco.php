<?php
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

ini_set("error_reporting","E_ERROR & ~E_WARNING & ~E_NOTICE");

include_once("../_database/athdbconn.php");

$intCodProduto = request("var_cod_produto");
$intCodPJ	   = request("var_cod_pj");

$objConn = abreDBConn(CFG_DB);

$strSQL = " SELECT out_preco_final FROM sp_menor_preco(" . $intCodProduto . ",'cad_pj'," . $intCodPJ . ",NULL)";
$objResult = $objConn->query($strSQL);

foreach($objResult as $objRS){
	echo(getValue($objRS,"out_preco_final"));
}

$objResult->closeCursor();
?>