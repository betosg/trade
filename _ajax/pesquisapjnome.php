<?php
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

ini_set("error_reporting","E_ERROR & ~E_WARNING & ~E_NOTICE");

include_once("../_database/athdbconn.php");

$strCNPJ         = request("var_cnpj");
$strRazaoSocial  = request("var_razao_social");
$strNomeFantasia = request("var_nome_fantasia");

$objConn = abreDBConn(CFG_DB);

if($strCNPJ != ""){
	$strSQLAux = "dpj.nome  =  'CNPJ'
				  AND dpj.valor = '" . $strCNPJ . "'";
}
else {
	$strSQLAux = "pj.nome_fantasia <=> '" . $strNomeFantasia . "%' OR pj.razao_social <=> '" . $strRazaoSocial . "'";
}

	$strSQL = " SELECT
				  pj.razao_social   AS nome
				, pj.cod_pj AS codigo
			 FROM 
				  cad_doc_pj AS dpj
				, cad_doc_tp AS dtp
				, cad_pj     AS pj
			WHERE " . $strSQLAux . "
			  AND dpj.nome  = dtp.nome
			  AND pj.cod_pj = dpj.cod_pj
			ORDER BY nome ASC limit 1
			";
$objResult = $objConn->query($strSQL);

foreach($objResult as $objRS){
	echo(getValue($objRS,"codigo") . ";" . getValue($objRS,"nome"));
}

$objResult->closeCursor();
?>