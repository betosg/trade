<?php
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

include_once("../_database/athdbconn.php");

$strCPF  = request("var_cpf");
$strNome = request("var_nome");

$objConn = abreDBConn(CFG_DB);

if($strCPF != "") {
	$strSQLAux = "dpf.nome  =  'CPF'
				  AND dpf.valor = '" . $strCPF . "'";
} elseif($strNome != "") {
	$strSQLAux = "pf.nome  =  '" . $strNome . "'";
}

try{
	$strSQL = " SELECT pf.cod_pf AS codigo
				     , pf.nome AS nome
				  FROM cad_doc_pf AS dpf INNER JOIN cad_pf AS pf ON (pf.cod_pf = dpf.cod_pf)
				 WHERE " . $strSQLAux;
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	header("HTTP/1.0 500 Server internal error");
	echo($e->getMessage());
	die();
}

$intI = 0;

foreach($objResult as $objRS) {
	if($intI != 0) { echo("\n"); }
	echo(getValue($objRS,"codigo") . "|" . getValue($objRS,"nome"));
	$intI++;
}

$objResult->closeCursor();
?>