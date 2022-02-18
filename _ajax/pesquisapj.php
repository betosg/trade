<?php
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

include_once("../_database/athdbconn.php");

$strCNPJ         = request("var_cnpj");
$strRazaoSocial  = request("var_razao_social");
$strNomeFantasia = request("var_nome_fantasia");

$objConn = abreDBConn(CFG_DB);

if($strCNPJ != ""){
	$strSQLAux = "dpj.nome = 'CNPJ'
				  AND dpj.valor = '" . $strCNPJ . "'";
} elseif($strNomeFantasia != "" || $strRazaoSocial != "") {
	$strSQLAux = "pj.nome_fantasia <=> '" . $strNomeFantasia . "%' OR pj.razao_social <=> '" . $strRazaoSocial . "'";
}

try {
	$strSQL = " SELECT pj.cod_pj AS codigo
					 , pj.razao_social
					 , pj.nome_fantasia
				  FROM cad_doc_pj AS dpj INNER JOIN cad_pj AS pj ON (pj.cod_pj = dpj.cod_pj) 
				 WHERE " . $strSQLAux . "
			  ORDER BY nome ASC
				";
	$objResult = $objConn->query($strSQL);
} catch(PDOException $e) {
	header("HTTP/1.0 500 Server internal error");
	echo($e->getMessage());
	die();
}

$intI = 0;

foreach($objResult as $objRS){
	if($intI != 0) { echo("\n"); }
	echo(getValue($objRS,"codigo") . "|" . getValue($objRS,"razao_social") . "|" . getValue($objRS,"nome_fantasia"));
	$intI++;
}

$objResult->closeCursor();
?>