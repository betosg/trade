<?php
header("Content-Type:text/html; charset=iso-8859-1");
header("Cache-Control:no-cache, must-revalidate");
header("Pragma:no-cache");

ini_set("error_reporting","E_ERROR & ~E_WARNING & ~E_NOTICE");

include_once("../_database/athdbconn.php");

$intCodPF    = request("var_pf");

$objConn = abreDBConn(CFG_DB);

$strSQL = " SELECT
				pj.razao_social
			FROM
				cad_pf_pj AS pfpj
			LEFT JOIN cad_pj AS pj ON pj.cod_pj = pfpj.cod_pj
			WHERE
					pfpj.cod_pf = " . $intCodPF . "
				AND pfpj.relacao = 'REAL'
				AND pfpj.categoria IS NULL ";
$objResult = $objConn->query($strSQL);

foreach($objResult as $objRS){
	echo(getValue($objRS,"razao_social"));
}

$objResult->closeCursor();
?>